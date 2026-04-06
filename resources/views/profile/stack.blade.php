<div class="mx-auto max-w-7xl">
    <div class="grid gap-4 lg:grid-cols-2 xl:grid-cols-3 xl:items-start">
        <div class="min-w-0 xl:col-span-1">
            @include('profile.partials.overview')
        </div>
        <div class="min-w-0 xl:col-span-1">
            @include('profile.partials.update-profile-information-form')
        </div>
        <div class="min-w-0 space-y-4 lg:col-span-2 xl:col-span-1">
            @include('profile.partials.sessions')
            @include('profile.partials.update-password-form')
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
