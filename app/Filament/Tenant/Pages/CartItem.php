<?php

namespace App\Filament\Tenant\Pages;

use App\Filament\Tenant\Pages\Traits\CartInteraction;
use App\Models\Tenants\CartItem as TenantsCartItem;
use App\Models\Tenants\PriceUnit;
use App\Models\Tenants\Product;
use App\Models\Tenants\Setting;
use App\Traits\HasTranslatableResource;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class CartItem extends Page
{
    use CartInteraction, HasTranslatableResource;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.tenant.pages.pos.cart-item';

    protected static string $layout = 'filament-panels::components.layout.base';

    public $cartItems = [];

    public $default_tax = 0;

    public function mount(): void
    {
        $this->refreshPage();
    }

    public function refreshCart(): void
    {
        $this->cartItems = TenantsCartItem::with('product')->get();
    }

    public function incrementQuantity(Product $product): void
    {
        $this->addCart($product);

        $this->refreshPage();
    }

    public function decrementQuantity(Product $product): void
    {
        $this->reduceCart($product);

        $this->refreshPage();
    }

    public function refreshPage(): void
    {
        $this->cartItems = TenantsCartItem::with('product.priceUnits')->get();
        $this->dispatch('cartUpdated', [
            'cartItems' => $this->cartItems,
            'default_tax' => $this->default_tax ?? Setting::get('default_tax', 0),
        ]);
    }

    public function updateDiscount(TenantsCartItem $cartItem, $discount)
    {
        $cartItem->discount_price = $discount;
        $cartItem->save();

        $this->refreshPage();
    }

    public function updatePriceUnit(TenantsCartItem $cartItem, PriceUnit $priceUnit): void
    {
        $cartItem->priceUnit()->associate($priceUnit);
        $cartItem->save();

        $this->refreshPage();

        Notification::make()
            ->title(__('Price unit update success'))
            ->success()
            ->send();
    }

    public function resetPriceUnit(TenantsCartItem $cartItem): void
    {
        $cartItem->priceUnit()->dissociate();

        $cartItem->save();
        $this->refreshPage();

        Notification::make()
            ->title(__('Price unit update success'))
            ->success()
            ->send();
    }
}
