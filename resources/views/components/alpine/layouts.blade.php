<div {{ $attributes }}>
  <template x-if="isLoading">
    <div class="fixed inset-0 bg-white bg-opacity-25 flex items-center justify-center z-50">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-500"></div>
    </div>
  </template>
  <div class="fixed top-0 left-0 right-0 bg-white z-10 shadow-sm">
    <div class="max-w-4xl mx-auto">
      <div class="flex items-center justify-between p-4">
        <button @click="history.back()" class="text-gray-800">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
        </button>
        @isset($title)
          <h1 class="text-xl font-semibold">{{ $title }}</h1>
        @endisset

        <div>
          @isset($rightAction)
            {{ $rightAction }}
          @endisset
        </div>
      </div>
    </div>
    @isset($headingBottom)
      {{ $headingBottom }}
    @endisset
  </div>

  {{ $slot }}
</div>
