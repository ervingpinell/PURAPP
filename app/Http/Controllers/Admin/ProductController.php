<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\ProductAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\HotelList;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['productType', 'images', 'prices.category', 'schedules', 'languages']);

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('slug', 'ilike', "%{$search}%");
            });
        }
        
        if ($request->has('type')) {
            $query->where('product_type_id', $request->get('type'));
        }
        
        if ($request->has('category')) {
            $query->where('product_category', $request->get('category'));
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(10);
        $types = ProductType::all();
        $hotels = HotelList::all();

        return view('admin.products.index', compact('products', 'types', 'hotels'));
    }

    public function create()
    {
        $types = ProductType::all();
        // Redirigir al wizard por defecto
        return redirect()->route('admin.products.wizard.start');
        // O retornar vista create simple
        // return view('admin.products.create', compact('types'));
    }

    public function store(Request $request)
    {
        // Lógica de guardado simple (si no se usa wizard)
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'product_type_id' => 'required|exists:product_types,product_type_id',
            'product_category' => 'required|string',
        ]);

        $product = Product::create($validated);
        
        ProductAuditLog::logAction('created', $product->product_id, auth()->id(), null, $product->toArray());

        return redirect()->route('admin.products.show', $product)
            ->with('success', __('Product created successfully'));
    }

    public function show(Product $product)
    {
        $product->load(['images', 'prices.category', 'availability', 'schedules', 'productType']);
        
        $stats = [
            'bookings_count' => $product->bookingDetails()->count(),
            'revenue' => $product->bookingDetails()->sum('total'),
            'views' => 0, // Implementar analytics si es necesario
        ];
        
        return view('admin.products.show', compact('product', 'stats'));
    }

    public function edit(Product $product)
    {
        $types = ProductType::all();
        return view('admin.products.edit', compact('product', 'types'));
    }

    public function update(Request $request, Product $product)
    {
        $oldValues = $product->toArray();
        
        $validated = $request->validate([
            'translations' => 'sometimes|array',
            'translations.*.name' => 'nullable|string|max:191',
            'translations.*.description' => 'nullable|string',
            'translations.*.overview' => 'nullable|string',
            'translations.*.recommendations' => 'nullable|string',
            'name' => 'sometimes|required|string|max:191',
            'product_type_id' => 'sometimes|required|exists:product_types,product_type_id',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'allow_custom_time' => 'sometimes|boolean',
            'allow_custom_pickup' => 'sometimes|boolean',
        ]);

        // Handle translations if provided (new tab-based approach)
        if (isset($validated['translations']) && is_array($validated['translations'])) {
            foreach ($validated['translations'] as $locale => $transData) {
                if (isset($transData['name'])) {
                    $product->setTranslation('name', $locale, $transData['name']);
                }
                if (isset($transData['description'])) {
                    $product->setTranslation('description', $locale, $transData['description']);
                }
                if (isset($transData['overview'])) {
                    $product->setTranslation('overview', $locale, $transData['overview']);
                }
                if (isset($transData['recommendations'])) {
                    $product->setTranslation('recommendations', $locale, $transData['recommendations']);
                }
            }
        } else {
            // Legacy single-field update (fallback for old forms)
            if (isset($validated['name'])) {
                $product->setTranslation('name', app()->getLocale(), $validated['name']);
            }
            if (isset($validated['description'])) {
                $product->setTranslation('description', app()->getLocale(), $validated['description']);
            }
        }


        // Update non-translatable fields only if provided
        if (isset($validated['product_type_id'])) {
            $product->product_type_id = $validated['product_type_id'];
        }
        if (isset($validated['is_active'])) {
            $product->is_active = $validated['is_active'];
        }
        if (isset($validated['allow_custom_time'])) {
            $product->allow_custom_time = $validated['allow_custom_time'];
        }
        if (isset($validated['allow_custom_pickup'])) {
            $product->allow_custom_pickup = $validated['allow_custom_pickup'];
        }
        
        $product->save();
        
        ProductAuditLog::logAction('updated', $product->product_id, auth()->id(), $oldValues, $product->toArray());

        return redirect()->route('admin.products.index')
            ->with('success', 'Traducciones actualizadas correctamente');
    }

    public function toggle(Product $product)
    {
        Log::info("ProductController: Toggling status for Product ID: {$product->product_id}. Current status: " . ($product->is_active ? 'Active' : 'Inactive'));

        $oldValues = $product->toArray();
        $product->update(['is_active' => !$product->is_active]);
        
        Log::info("ProductController: Product ID: {$product->product_id} toggled. New status: " . ($product->is_active ? 'Active' : 'Inactive'));

        ProductAuditLog::logAction('updated', $product->product_id, auth()->id(), $oldValues, $product->toArray());

        return back()->with('success', __('Product status updated successfully'));
    }

    public function destroy(Product $product)
    {
        $oldValues = $product->toArray();
        $product->delete();
        
        ProductAuditLog::logAction('deleted', $product->product_id, auth()->id(), $oldValues, null);

        return redirect()->route('admin.products.index')
            ->with('success', __('Product deleted successfully'));
    }

    public function trash()
    {
         $products = Product::onlyTrashed()->orderBy('deleted_at', 'desc')->paginate(10);
         return view('admin.products.index', compact('products'))->with('trash', true);
    }

    public function restore($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();
        ProductAuditLog::logAction('restored', $product->product_id, auth()->id(), null, null);
        
        return back()->with('success', __('Product restored successfully'));
    }

    public function forceDelete($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $oldValues = $product->toArray();
        $product->forceDelete();
        ProductAuditLog::logAction('force_deleted', $product->product_id, auth()->id(), $oldValues, null);
        
        return back()->with('success', __('Product permanently deleted'));
    }
    
    // Métodos específicos de Product2
    
    public function duplicate(Product $product)
    {
        $newProduct = $product->replicate();
        $newProduct->name = $product->name . ' (Copy)';
        $newProduct->slug = Str::slug($newProduct->name) . '-' . uniqid();
        $newProduct->is_active = false;
        $newProduct->save();
        
        // Duplicar relaciones (precios, imágenes, etc.) si es necesario
        
        ProductAuditLog::logAction('created', $newProduct->product_id, auth()->id(), null, $newProduct->toArray(), 'Duplicated from ' . $product->product_id);
        
        return redirect()->route('admin.products.edit', $newProduct)
            ->with('success', __('Product duplicated successfully'));
    }

    public function exportExcel()
    {
        // Implementation pending or use package
        return back()->with('warning', 'Export functionality coming soon');
    }

    // Stats stubs
    public function draftsStats() { return response()->json(['count' => 0]); }
    public function usersStats() { return response()->json(['count' => 0]); }
    public function activityStats() { return response()->json(['count' => 0]); }
}
