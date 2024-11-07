@props([
  'cancelAction' => '',
  'closeAction' => '',
  'confirmAction' => '',
  'confirmTitle' => 'Save changes',
  'cancelTitle' => 'Cancel',
  'show' => 'false'
])
<div>
  <div x-show="{{$show}}"
    class="fixed inset-0 z-50 overflow-y-auto"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center">
      <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

      <div class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle w-full sm:p-6">
        <div class="absolute top-0 right-0 pt-4 pr-4">
          <button @click="{{ $closeAction }}" class="text-gray-400 hover:text-gray-500">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        @if(isset($heading))
          <div class="border-b w-full pb-2">
            {{ $heading }}
          </div>
        @endif

        {{ $slot }}

        <div class="mt-5 sm:mt-4 flex sm:flex-row-reverse gap-x-2">
          <button
            type="button"
            class="inline-flex w-full justify-center rounded-full border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2"
            @click="{{$cancelAction}}" >
            {{ $cancelTitle }}
          </button>
          <button
            type="button"
            @click="{{$confirmAction}}"
            class="inline-flex w-full justify-center rounded-full border border-transparent bg-orange-500 px-4 py-2 text-sm font-medium text-white hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2" >
            {{ $confirmTitle }}
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
