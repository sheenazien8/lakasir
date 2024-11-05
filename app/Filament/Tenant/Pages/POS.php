<?php

namespace App\Filament\Tenant\Pages;

use App\Filament\Tenant\Pages\Traits\CartInteraction;
use App\Models\Tenants\CartItem;
use App\Models\Tenants\Category;
use App\Models\Tenants\Product;
use App\Traits\HasTranslatableResource;
use Filament\Pages\Page;

class POS extends Page
{
    use CartInteraction, HasTranslatableResource;

    public static ?string $label = 'POS V2';

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static string $view = 'filament.tenant.pages.pos.index';

    protected static string $layout = 'filament-panels::components.layout.base';

    public $menuItems = [];

    public $categories = [];

    public $cartItems = [];

    public $page = 1;

    public $hasMorePages = true;

    public $perPage = 20;

    public $currentCategory = 'all';

    public $isLoading = false;

    public $search = '';

    public function mount()
    {
        $this->allCategory();
        $this->categories = array_merge([
            [
                'id' => 'all',
                'name' => 'All',
            ],
        ], Category::query()
            ->get()
            ->toArray());
        $this->refreshCart();
    }

    public function refreshCart()
    {
        $this->cartItems = CartItem::get()->toArray();
    }

    public function loadMore()
    {
        $this->page++;

        $query = Product::query();

        if ($this->currentCategory !== 'all') {
            $query->whereCategoryId($this->currentCategory);
        }

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        $newItems = $query->skip(($this->page - 1) * $this->perPage)
            ->take($this->perPage)
            ->get();

        if ($newItems->count() < $this->perPage) {
            $this->hasMorePages = false;
        }

        $this->menuItems = collect($this->menuItems)->concat($newItems);

        $this->dispatch('refreshPage', [
            'cartItems' => $this->cartItems,
            'categories' => $this->categories,
            'menuItems' => $this->menuItems,
            'hasMorePages' => $this->hasMorePages,
        ]);
    }

    public function searchProduct($query)
    {
        $this->search = $query;
        $this->page = 1;

        $query = Product::query();

        if ($this->currentCategory !== 'all') {
            $query->whereCategoryId($this->currentCategory);
        }

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%")
                ->orWhere('barcode', 'like', "%{$this->search}%")
                ->orWhere('sku', 'like', "%{$this->search}%");
        }

        $this->menuItems = $query->take($this->perPage)->get();
        $this->hasMorePages = $query->count() > $this->perPage;

        $this->dispatch('refreshPage', [
            'cartItems' => $this->cartItems,
            'categories' => $this->categories,
            'menuItems' => $this->menuItems,
            'hasMorePages' => $this->hasMorePages,
        ]);
    }

    public function resetSearch()
    {
        $this->search = '';
        $this->page = 1;

        if ($this->currentCategory === 'all') {
            $this->allCategory();
        } else {
            $category = Category::find($this->currentCategory);
            if ($category) {
                $this->filterCategoryId($category);
            } else {
                $this->allCategory();
            }
        }
    }

    public function addToCart(Product $product, ?array $data = null): void
    {
        $this->addCart($product, $data);
        $this->dispatch('refreshPage', [
            'cartItems' => $this->cartItems,
            'categories' => $this->categories,
            'menuItems' => $this->menuItems,
            'hasMorePages' => $this->hasMorePages,
        ]);
    }

    public function allCategory(): void
    {
        $this->page = 1;
        $this->currentCategory = 'all';
        $this->menuItems = Product::query()
            ->when($this->search, fn ($query) => $query->where('name', 'like', "%{$this->search}%")
            )
            ->take($this->perPage)
            ->get();

        $this->hasMorePages = Product::when($this->search, fn ($query) => $query->where('name', 'like', "%{$this->search}%")
        )->count() > $this->perPage;

        $this->dispatch('refreshPage', [
            'cartItems' => $this->cartItems,
            'categories' => $this->categories,
            'menuItems' => $this->menuItems,
            'hasMorePages' => $this->hasMorePages,
        ]);
    }

    public function filterCategoryId(?Category $category): void
    {
        $this->page = 1;
        $this->currentCategory = $category->id;

        $query = Product::whereCategoryId($category->id);

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        $this->menuItems = $query->take($this->perPage)->get();
        $this->hasMorePages = $query->count() > $this->perPage;

        $this->dispatch('refreshPage', [
            'cartItems' => $this->cartItems,
            'categories' => $this->categories,
            'menuItems' => $this->menuItems,
            'hasMorePages' => $this->hasMorePages,
        ]);
    }

    public function scanProduct(string $barcode): void
    {
        $this->addCartUsingScanner($barcode);
        $this->dispatch('refreshPage', [
            'cartItems' => $this->cartItems,
            'categories' => $this->categories,
            'menuItems' => $this->menuItems,
            'hasMorePages' => $this->hasMorePages,
        ]);
    }
}
