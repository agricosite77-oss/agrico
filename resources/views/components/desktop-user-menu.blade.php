<flux:dropdown position="bottom" align="end">
    <flux:profile
        :name="auth()->user()?->name ?? 'Admin'"
        icon-trailing="chevron-down"
    />

    <flux:menu>
        <flux:menu.item :href="route('dashboard')" icon="layout-grid">
            {{ __('Dashboard') }}
        </flux:menu.item>

        <flux:menu.separator />

        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                {{ __('Log Out') }}
            </flux:menu.item>
        </form>
    </flux:menu>
</flux:dropdown>
