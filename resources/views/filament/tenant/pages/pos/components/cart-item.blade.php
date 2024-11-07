<x-alpine.layouts x-data="cart()"
    class="relative min-h-screen bg-gray-50">

  <x-slot:title>Cart</x-slot:title>

  <x-slot:rightAction>
    <div class="relative" x-data="{
        isOpen: false,
        isEditModalOpen: false,
        isConfirmDialogOpen: false,
        confirmAction: null,
        confirmMessage: '',
        confirmTitle: '',
        editForm: {
          memberDiscount: 0,
          taxRate: 0
        }
      }">
      <!-- Dropdown Trigger -->
      <button @click="isOpen = !isOpen" class="text-gray-800">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>

      <!-- Dropdown Menu -->
      <div x-show="isOpen"
        @click.away="isOpen = false"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
        <div class="py-1">
          <button @click="isEditModalOpen = true; isOpen = false"
            class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Edit Cart Details
          </button>
          <button @click="
            confirmTitle = 'Clear Cart';
            confirmMessage = 'Are you sure you want to clear all items from the cart? This action cannot be undone.';
            confirmAction = clearCart;
            isConfirmDialogOpen = true;
            isOpen = false"
            class="w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-gray-100 flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            Clear Cart
          </button>
        </div>
      </div>

      <div x-show="isConfirmDialogOpen"
        class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
          <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

          <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                  <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                  <h3 class="text-lg leading-6 font-medium text-gray-900" x-text="confirmTitle"></h3>
                  <div class="mt-2">
                    <p class="text-sm text-gray-500" x-text="confirmMessage"></p>
                  </div>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <button @click="confirmAction(); isConfirmDialogOpen = false"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                Confirm
              </button>
              <button @click="isConfirmDialogOpen = false"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                Cancel
              </button>
            </div>
          </div>
        </div>
      </div>

      <x-alpine.modal
        cancelAction="isEditModalOpen = false"
        closeAction="isEditModalOpen = false"
        show="isEditModalOpen"
        confirmAction="updateCartDetails(editForm); isEditModalOpen = false">
        <x-slot:heading>
          <h3 class="text-lg font-medium leading-6 text-gray-900">
            Edit Cart Details
          </h3>
        </x-slot:heading>
        <div class="sm:flex sm:items-start">
          <div class="w-full mt-3 text-center sm:mt-0 sm:text-left">
            <div class="mt-4 space-y-4">
              <!-- Member Discount Input -->
              <div>
                <label class="block text-sm font-medium text-gray-700">
                  Member Discount (%)
                </label>
                <input type="number"
                x-model="editForm.memberDiscount"
                class="w-full px-3 py-2 mt-1 text-sm border rounded-lg focus:outline-none focus:ring-1 focus:ring-orange-500"
                min="0"
                max="100">
              </div>

              <!-- Tax Rate Input -->
              <div>
                <label class="block text-sm font-medium text-gray-700">
                  Tax Rate (%)
                </label>
                <input type="number"
                x-model="editForm.taxRate"
                class="w-full px-3 py-2 mt-1 text-sm border rounded-lg focus:outline-none focus:ring-1 focus:ring-orange-500"
                min="0"
                max="100">
              </div>
            </div>
          </div>
        </div>
      </x-alpine.modal>
    </div>

  </x-slot:rightAction>

  <div class="max-w-4xl mx-auto p-4 pt-20 pb-80">
    <div class="space-y-4">
      <template x-for="(cartItem, itemId) in cart" :key="itemId">
        <div class="bg-white rounded-lg p-4">
          <div class="flex items-center space-x-4">
            <img :src="cartItem.product?.hero_image" class="w-16 h-16 object-cover rounded-lg">
            <div class="flex-1">
              <div class="flex justify-between items-start">
                <div class="mr-2">
                  <h3 class="font-medium text-gray-900" x-text="cartItem.product?.name"></h3>
                  <p class="text-sm text-gray-500" x-text="cartItem.product?.description"></p>
                </div>
                <div class="flex items-center space-x-2">
                  <button
                    @click="decrementQuantity(cartItem.product_id)"
                    class="w-6 h-6 rounded-full border border-orange-500 text-lakasir-primary flex items-center justify-center"
                    >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                    </svg>
                  </button>
                  <span class="w-6 text-center" x-text="cartItem.qty"></span>
                  <button
                    @click="incrementQuantity(cartItem.product_id)"
                    class="w-6 h-6 rounded-full bg-orange-500 text-white flex items-center justify-center"
                    >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Price Section -->
          <div class="mt-3 space-y-1">
            <div class="text-sm">
              <span class="text-gray-500">Total Price</span>
            </div>
            <div class="flex items-center justify-between mt-1">
              <div>
                <span class="text-sm">Rp.</span>
                <span class="font-medium" x-text="`${moneyFormat(cartItem.price_unit_value / cartItem.qty)} x (${cartItem.qty} ${cartItem.product.unit})`"></span>
              </div>
            </div>

            <!-- Discount Input -->
            <div class="mt-2 flex items-center space-x-2">
              <div class="flex-1">
                <label class="text-gray-500 text-sm" for="sub_total">Sub total</label>
                <p class="font-medium">
                  <span class="text-sm">Rp.</span>
                  <span class="font-medium" x-text="`Rp. ${moneyFormat(cartItem.price_unit_value)}`"></span>
                </p>
              </div>
              {{-- <div class="flex justify-items-center justify-center h-0.5 w-3 bg-black mt-5 rounded-sm"></div> --}}
              <div class="flex-1">
                <label for="discount">Add discount</label>
                <input type="number"
                  id="discount"
                  :value="cartItem.discount_price || ''"
                  @input.debounce.500ms="updateDiscount(cartItem.id, $event.target.value)"
                  class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-1 focus:ring-orange-500 read-only:bg-gray-200"
                  placeholder="Add Discount"
                >
              </div>
            </div>
            <div class="flex justify-between items-end">
              <div>
                <p class="text-gray-500 text-sm">Total</p>
                <p><span class="text-sm text-lakasir-primary">Rp. </span> <span x-text="moneyFormat(cartItem.price_unit_value - cartItem.discount_price)" class="font-medium "></span> </p>
              </div>
              <button
                @click="openPriceModal(cartItem)"
                x-show="cartItem.product?.price_units?.length > 0"
                class="text-sm text-lakasir-primary underline flex gap-x-2">
                <span>Price setting</span>
              </button>
            </div>
          </div>
        </div>
      </template>
    </div>

    <button @click="goToMenu"
      class="w-full mt-6 p-4 border-2 border-orange-500 text-lakasir-primary rounded-full font-medium flex items-center justify-center"
      >
      <span class="mr-2">+</span>
      Add More
    </button>
  </div>

  <div class="fixed bottom-0 left-0 right-0 bg-white border-t">
    <div class="max-w-4xl mx-auto p-4">
      <div class="space-y-3">
        <div class="flex justify-between text-gray-600">
          <span class="text-gray-500 text-sm">Sub total</span>
          <span x-text="'Rp. ' + moneyFormat(cartTotal)"></span>
        </div>
        <div class="flex justify-between text-gray-600">
          <span class="text-gray-500 text-sm underline">Discount</span>
          <span x-text="`Rp. ${moneyFormat(totalDiscount)}`"></span>
        </div>
        <div class="flex justify-between text-gray-600">
          <span class="text-gray-500 text-sm underline">Member</span>
          <span>No Member</span>
        </div>
        <div class="flex justify-between text-gray-600">
          <span class="text-gray-500 text-sm underline" x-text="`Tax (${default_tax})%`"></span>
          <span x-text="`Rp. ${moneyFormat(cartTotal * default_tax / 100)}`"></span>
        </div>
        <div class="flex justify-between font-medium text-lg">
          <span>Total</span>
          <span class="text-lakasir-primary" x-text="`Rp. ${moneyFormat(cartTotal - (cartTotal * default_tax / 100))}`"></span>
        </div>
      </div>

      <button
        @click="continueToPayment"
        class="w-full mt-6 px-4 py-6 bg-lakasir-primary text-white rounded-full font-medium"
        >
        Continue Payment
      </button>
    </div>
  </div>
  <x-alpine.modal
    closeAction="showPriceModal = false; priceUnitChoosed = null;"
    cancelAction="resetPriceUnit()"
    show="showPriceModal"
    cancelTitle="Reset"
    confirmAction="updatePriceUnit()">
    <x-slot:heading>
      Price setting
    </x-slot:heading>
    <div class="sm:flex sm:items-start mt-5">
      <template x-for="(priceUnit, unitId) in priceUnitItems?.product?.price_units" :key="unitId">
        <div class="flex items-center gap-x-4" x-id="['price-unit']">
          <input type="radio" :id="$id('price-unit', unitId)" :value="priceUnit.id" x-model="priceUnitChoosed">
          <label :for="$id('price-unit', unitId)" x-text="`${priceUnit.unit} - ${moneyFormat(priceUnit.selling_price)}`"></label>
        </div>
      </template>
    </div>
  </x-alpine.modal>
</x-alpine.layouts>

@script()
<script>
  Alpine.data('cart', () => {
    return {
      cart: @json($cartItems) || {},
      isLoading: false,
      default_tax: @json($default_tax),
      showPriceModal: false,
      priceUnitItems: null,
      priceUnitChoosed: null,

      init() {
        Livewire.on('cartUpdated', (data) => {
          this.cart = data[0].cartItems;
        });
        // this.$watch('$wire.cartItems', value => cart = value)
      },

      async clearCart() {
        try {
          this.isLoading = true;
          await $wire.clearCart();
          Livewire.navigate('/member/p-o-s')
        } catch (error) {
          console.error('Error clearing cart:', error);
        } finally {
          this.isLoading = false;
        }
      },

      async updateCartDetails(formData) {
        try {
          this.isLoading = true;
          await $wire.updateCartDetails({
            memberDiscount: parseFloat(formData.memberDiscount) || 0,
            taxRate: parseFloat(formData.taxRate) || 0
          });
          this.default_tax = formData.taxRate;
        } catch (error) {
          console.error('Error updating cart details:', error);
        } finally {
          this.isLoading = false;
        }
      },

      openPriceModal(item) {
        this.showPriceModal = true;
        this.priceUnitItems = item;
      },

      async updatePriceUnit() {
        await $wire.updatePriceUnit(this.priceUnitItems.id, this.priceUnitChoosed);

        this.showPriceModal = false;
        this.priceUnitItems = null;
        this.priceUnitChoosed = null;
      },

      async resetPriceUnit() {
        await $wire.resetPriceUnit(this.priceUnitItems.id);

        this.showPriceModal = false;
        this.priceUnitItems = null;
        this.priceUnitChoosed = null;
      },

      get cartTotal() {
        return Object.values(this.cart).reduce((total, cartItem) => {
          const subtotal = cartItem.price_unit_value;
          const discount = cartItem.discount_price || 0;
          return total + (subtotal - discount);
        }, 0);
      },

      get totalDiscount() {
        return Object.values(this.cart).reduce((total, cartItem) => {
          const discount = cartItem.discount_price || 0;
          console.log(discount)
          return total + discount;
        }, 0);
      },

      async updateDiscount(cart_id, discount) {
        console.log(cart_id)
        try {
          this.isLoading = true;
          await $wire.updateDiscount(cart_id, parseFloat(discount) || 0);
        } finally {
          this.isLoading = false;
        }
      },

      async decrementQuantity(product_id) {
        try {
          this.isLoading = true
          await $wire.decrementQuantity(product_id);
        } finally {
          this.isLoading = false
        }
      },

      async incrementQuantity(product_id) {
        try {
          this.isLoading = true
          await $wire.incrementQuantity(product_id)
        } finally {
          this.isLoading = false
        }
      },

      goToMenu() {
        Livewire.navigate('/member/p-o-s')
      },

      continueToPayment() {
        console.log('Proceeding to payment...');
        console.log('Cart Total:', this.cartTotal);
        console.log('Tax:', this.cartTotal * 0.1);
        console.log('Final Total:', this.cartTotal * 1.1);
      }
    }
  })
</script>
@endscript
