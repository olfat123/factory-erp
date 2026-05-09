<div class="flex items-center gap-1">
    <a
        href="{{ route('locale.switch', 'en') }}"
        class="fi-btn fi-btn-size-sm fi-btn-color-gray fi-size-sm fi-btn-outlined {{ app()->getLocale() === 'en' ? 'fi-color-primary fi-btn-color-primary' : '' }} px-2 py-1 text-sm font-medium rounded"
    >
        EN
    </a>
    <a
        href="{{ route('locale.switch', 'ar') }}"
        class="fi-btn fi-btn-size-sm fi-btn-color-gray fi-size-sm fi-btn-outlined {{ app()->getLocale() === 'ar' ? 'fi-color-primary fi-btn-color-primary' : '' }} px-2 py-1 text-sm font-medium rounded"
    >
        عربي
    </a>
</div>
