<x-alpine.layouts x-data="pos()" class="relative min-h-screen bg-gray-50">
<style>
@keyframes scan {
  0% {
    transform: translateY(-100px);
  }
  50% {
    transform: translateY(100px);
  }
  100% {
    transform: translateY(-100px);
  }
}
</style>
  <x-slot:title>CASHIER</x-slot:title>
  <x-slot:headingBottom>
    <div class="relative px-4">
      <div class="flex gap-2">
        <div class="relative flex-1">
          <input type="text"
            x-model="searchQuery"
            @input="handleSearch"
            class="w-full px-4 py-2 bg-gray-100 rounded-lg pl-10"
            placeholder="Search">
          <div class="absolute inset-y-0 left-3 flex items-center">
            <template x-if="!isSearching">
              <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </template>
            <template x-if="isSearching">
              <div class="animate-spin h-5 w-5 border-2 border-orange-500 border-b-transparent rounded-full"></div>
            </template>
          </div>
        </div>
        @include('filament.tenant.pages.pos.components.barcode-scanner')
      </div>
    </div>

    <!-- Categories -->
    <div class="flex gap-x-8 p-4 overflow-x-auto">
      <template x-for="cat in categories" :key="cat.id">
        <button
          @click="filterCategory(cat.id)"
          class="pb-2 whitespace-nowrap"
          :class="selectedCategory === cat.id ? 'text-orange-500 border-b-2 border-orange-500' : 'text-gray-500'"
          x-text="cat.name"
        ></button>
      </template>
    </div>
  </x-slot:headingBottom>

  <!-- Main Content with appropriate top padding -->
  <div class="max-w-4xl mx-auto p-4 pt-40 pb-32">
    <div class="space-y-4">
      <template x-for="item in items" :key="item.id">
        <div class="flex items-center space-x-4 bg-white rounded-lg p-2">
          <div class="relative w-24 h-24 flex-shrink-0">
            <img
              :src="item.hero_image"
              class="w-full h-full object-cover rounded-lg"
              :alt="item.name"
            >
            <template x-if="item.stock === 0">
              <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg flex items-center justify-center">
                <span class="text-white text-sm font-medium">Out of stock</span>
              </div>
            </template>
            <template x-if="item.stock > 0 && getItemQuantity(item) == 0">
              <div class="absolute bottom-0 left-0 bg-black bg-opacity-50 text-white text-sm p-1 rounded-bl-lg rounded-br-lg w-full text-center">
                <span x-text="`${item.stock} stock`"></span>
              </div>
            </template>
            <template x-if="getItemQuantity(item) > 0">
              <div class="absolute bottom-0 left-0 bg-orange-500 text-white text-sm p-1 rounded-bl-lg rounded-br-lg w-full text-center">
                <span x-text="`${getItemQuantity(item)} Selected`"></span>
              </div>
            </template>
          </div>

          <div class="flex-1">
            <h3 class="font-medium" x-text="item.name"></h3>
            <p class="text-sm text-gray-500" x-text="item.description"></p>
            <div class="mt-2">
              <p class="text-sm text-gray-500">Total Price</p>
              <p class="text-gray-900 font-semibold">
                <span class="text-lakasir-primary text-sm font-normal" x-text="`Rp. `"></span>
                <span x-text="moneyFormat(item.selling_price)"></span>
              </p>
            </div>
          </div>

          <template x-if="getItemQuantity(item) === 0">
            <div x-data="item">
              @include('filament.tenant.pages.pos.components.add-to-cart-button')
            </div>
          </template>
          <template x-if="getItemQuantity(item) > 0">
            <div class="text-orange-500 px-4 py-1 rounded-full border-2 border-orange-500">
              Added
            </div>
          </template>
        </div>
      </template>
    </div>
    <div id="infinite-scroll-sentinel" class="h-4 w-full">
      <template x-if="isLoadingMore">
        <div class="flex justify-center p-4">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-500"></div>
        </div>
      </template>
    </div>
  </div>

  <template x-if="cartTotal > 0">
    <div class="fixed bottom-0 left-0 right-0 bg-white shadow-lg border-t px-4 py-4"
      @click="proceedToCheckout">
      <div class="max-w-4xl mx-auto bg-lakasir-primary rounded-full">
        <div class="flex items-center justify-between px-3 py-2.5 text-white">
          <div class="grid items-center ml-3">
            <p class="font-medium" x-text="`${cartItemCount} Items`"></p>
            <p class="">Selected</p>
          </div>
          <div class="flex items-center">
            <div class="mr-2">
              <p class="text-sm">Total</p>
              <p class="font-medium" x-text="moneyFormat(cartTotal)"></p>
            </div>
            <div class="text-white p-3.5 rounded-full flex items-center gap-2 border border-white">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>

</x-alpine.layouts>

@script()
<script>
  Alpine.data('pos', () => {
    return {
      items: @json($menuItems),
      categories: @json($categories),
      searchQuery: '',
      selectedCategory: 'all',
      cart: @json($cartItems) || {},
      isLoading: false,
      isSearching: false,
      isCheckingOut: false,
      isScanning: false,
      hasMorePages: true,
      isLoadingMore: false,
      searchTimeout: null,

      init() {
        this.setupInfiniteScroll();

        Livewire.on('refreshPage', (data) => {
          this.cart = data[0].cartItems;
          this.items = data[0].menuItems;
          this.hasMorePages = data[0].hasMorePages;
          this.page = data[0].page;
          this.perPage = data[0].perPage;
        });

        this.$watch('searchQuery', (value) => {
          // Clear the previous timeout
          if (this.searchTimeout) {
            clearTimeout(this.searchTimeout);
          }

          // Reset search if empty
          if (!value || value.length === 0) {
            this.isSearching = true;
            $wire.resetSearch().then(() => {
              this.isSearching = false;
            });
            return;
          }

          // Set a new timeout for search
          if (value.length >= 2) {
            this.searchTimeout = setTimeout(() => {
              this.handleSearch();
            }, 300);
          }
        });
      },

      setupInfiniteScroll() {
        const observer = new IntersectionObserver(async (entries) => {
          const target = entries[0];
          if (target.isIntersecting && this.hasMorePages && !this.isLoadingMore) {
            await this.loadMore();
          }
        }, {
          root: null,
          rootMargin: '100px',
          threshold: 0.1
        });

        // Observe the sentinel element
        const sentinel = document.querySelector('#infinite-scroll-sentinel');
        if (sentinel) {
          observer.observe(sentinel);
        }
      },

      async loadMore() {
        if (this.isLoadingMore || !this.hasMorePages) return;

        this.isLoadingMore = true;
        try {
          await $wire.loadMore();
        } finally {
          this.isLoadingMore = false;
        }
      },

      getItemQuantity(item) {
        const product = this.cart.filter(cart => {
          return cart.product_id == item.id
        })[0]

        return product?.qty || 0;
      },

      get cartTotal() {
        let cartTotalValue = Object.values(this.cart).reduce((total, cartItem) => {
          return total + (cartItem.price_unit_value);
        }, 0);

        return cartTotalValue;
      },

      get cartItemCount() {
        return Object.values(this.cart).reduce((count, cartItem) => {
          return count + 1;
        }, 0);
      },

      async filterCategory(category) {
        this.isLoading = true;
        this.selectedCategory = category;

        try {
          if (category == 'all') {
            await $wire.allCategory();
          } else {
            await $wire.filterCategoryId(category);
          }
        } finally {
          this.isLoading = false;
        }
      },

      async handleSearch() {
        if (this.searchQuery.length < 3) return;

        this.isSearching = true;
        try {
          await new Promise(resolve => setTimeout(resolve, 1000));
          await $wire.searchProduct(this.searchQuery);
        } finally {
          this.isSearching = false;
        }
      },

      async addToCart(productId, data) {
        this.isLoading = true;
        try {
          await $wire.addToCart(productId, data);
        } finally {
          this.isLoading = false;
        }
      },

      async proceedToCheckout() {
        if (this.isCheckingOut) return;

        this.isCheckingOut = true;
        try {
          await Livewire.navigate('/member/cart-item');
        } finally {
          this.isCheckingOut = false;
        }
      }
    }
  })
</script>
@endscript

