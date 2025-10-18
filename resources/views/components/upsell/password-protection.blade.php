<div class="settings-card pro-feature bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-purple-800">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('upsell.password_protection.title') }}</h2>
            @include("components.badges.upgrade-badge")
        </div>
        <label class="relative inline-flex items-center cursor-pointer">
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 dark:peer-focus:ring-purple-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-500 peer-checked:bg-purple-600"></div>
        </label>
    </div>
    
    <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg mb-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-crown text-purple-500 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-purple-800 dark:text-purple-200">
                    {{ __('upsell.password_protection.feature_description') }}
                </h3>
                <div class="mt-2 text-sm text-purple-700 dark:text-purple-300">
                    <p><a href="{{ route('subscription.pricing') }}" class="underline text-white">{{ __('upsell.password_protection.upgrade_message') }}</a></p>
                </div>
            </div>
        </div>
    </div>
</div>