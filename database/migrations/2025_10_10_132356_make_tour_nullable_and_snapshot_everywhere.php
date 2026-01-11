<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected ?string $viewSql = null;

    public function up(): void
    {
        // 1) Guardar SQL de la vista (si existe) y dropearla
        try {
            $row = DB::selectOne("SELECT pg_get_viewdef('v_booking_facts'::regclass, true) AS sql");
            $this->viewSql = $row?->sql ?? null;
        } catch (\Throwable $e) {
            $this->viewSql = null; // la vista no existe
        }

        try {
            DB::statement('DROP VIEW IF EXISTS v_booking_facts');
        } catch (\Throwable $e) {
            // ignora
        }

        // 2) BOOKINGS: quitar FK actual, hacer nullable, snapshot, FK con ON DELETE SET NULL
        $fkBookings = DB::selectOne("
            SELECT conname AS name
            FROM pg_constraint c
            JOIN pg_class r ON r.oid = c.conrelid
            WHERE r.relname = 'bookings'
              AND c.contype = 'f'
              AND pg_get_constraintdef(c.oid) ILIKE '%(tour_id)%'
            LIMIT 1
        ");
        if ($fkBookings?->name) {
            DB::statement("ALTER TABLE bookings DROP CONSTRAINT {$fkBookings->name}");
        }

        DB::statement("ALTER TABLE bookings ALTER COLUMN tour_id DROP NOT NULL");
        DB::statement("ALTER TABLE bookings ADD COLUMN IF NOT EXISTS tour_name_snapshot varchar(255)");

        DB::statement("
            ALTER TABLE bookings
            ADD CONSTRAINT bookings_tour_id_fkey
            FOREIGN KEY (tour_id) REFERENCES tours(tour_id) ON DELETE SET NULL
        ");

        // 3) BOOKING_DETAILS: quitar FK actual, hacer nullable, snapshot, FK con ON DELETE SET NULL
        $fkDetails = DB::selectOne("
            SELECT conname AS name
            FROM pg_constraint c
            JOIN pg_class r ON r.oid = c.conrelid
            WHERE r.relname = 'booking_details'
              AND c.contype = 'f'
              AND pg_get_constraintdef(c.oid) ILIKE '%(tour_id)%'
            LIMIT 1
        ");
        if ($fkDetails?->name) {
            DB::statement("ALTER TABLE booking_details DROP CONSTRAINT {$fkDetails->name}");
        }

        DB::statement("ALTER TABLE booking_details ALTER COLUMN tour_id DROP NOT NULL");
        DB::statement("ALTER TABLE booking_details ADD COLUMN IF NOT EXISTS tour_name_snapshot varchar(255)");

        DB::statement("
            ALTER TABLE booking_details
            ADD CONSTRAINT booking_details_tour_id_fkey
            FOREIGN KEY (tour_id) REFERENCES tours(tour_id) ON DELETE SET NULL
        ");

        // 4) (Opcional) backfill de snapshots existentes usando Eloquent
        // Skip if tables are empty (fresh migration)
        $bookingCount = DB::table('bookings')->count();
        if ($bookingCount > 0) {
            \App\Models\Booking::with('tour')
                ->whereNotNull('tour_id')
                ->where(function ($q) {
                    $q->whereNull('tour_name_snapshot')
                        ->orWhere('tour_name_snapshot', '');
                })
                ->chunk(100, function ($bookings) {
                    foreach ($bookings as $booking) {
                        if ($booking->tour) {
                            $booking->update(['tour_name_snapshot' => $booking->tour->name]);
                        }
                    }
                });

            \App\Models\BookingDetail::with('tour')
                ->whereNotNull('tour_id')
                ->where(function ($q) {
                    $q->whereNull('tour_name_snapshot')
                        ->orWhere('tour_name_snapshot', '');
                })
                ->chunk(100, function ($details) {
                    foreach ($details as $detail) {
                        if ($detail->tour) {
                            $detail->update(['tour_name_snapshot' => $detail->tour->name]);
                        }
                    }
                });
        }

        // 5) Recrear la vista (si existía)
        if ($this->viewSql) {
            DB::statement("CREATE VIEW v_booking_facts AS {$this->viewSql}");
        }
    }

    public function down(): void
    {
        // Guardar SQL actual de la vista y dropear
        try {
            $row = DB::selectOne("SELECT pg_get_viewdef('v_booking_facts'::regclass, true) AS sql");
            $this->viewSql = $row?->sql ?? null;
        } catch (\Throwable $e) {
            $this->viewSql = null;
        }
        try {
            DB::statement('DROP VIEW IF EXISTS v_booking_facts');
        } catch (\Throwable $e) {
        }

        // Revertir cambios (columna NOT NULL y FK a ON DELETE RESTRICT)
        // booking_details
        DB::statement("ALTER TABLE booking_details DROP CONSTRAINT IF EXISTS booking_details_tour_id_fkey");
        DB::statement("ALTER TABLE booking_details ALTER COLUMN tour_id SET NOT NULL");
        DB::statement("
            ALTER TABLE booking_details
            ADD CONSTRAINT booking_details_tour_id_fkey
            FOREIGN KEY (tour_id) REFERENCES tours(tour_id) ON DELETE RESTRICT
        ");
        // si quieres quitar el snapshot:
        // DB::statement("ALTER TABLE booking_details DROP COLUMN IF EXISTS tour_name_snapshot");

        // bookings
        DB::statement("ALTER TABLE bookings DROP CONSTRAINT IF EXISTS bookings_tour_id_fkey");
        DB::statement("ALTER TABLE bookings ALTER COLUMN tour_id SET NOT NULL");
        DB::statement("
            ALTER TABLE bookings
            ADD CONSTRAINT bookings_tour_id_fkey
            FOREIGN KEY (tour_id) REFERENCES tours(tour_id) ON DELETE RESTRICT
        ");
        // si quieres quitar el snapshot:
        // DB::statement("ALTER TABLE bookings DROP COLUMN IF EXISTS tour_name_snapshot");

        // Recrear la vista si existía
        if ($this->viewSql) {
            DB::statement("CREATE VIEW v_booking_facts AS {$this->viewSql}");
        }
    }
};
