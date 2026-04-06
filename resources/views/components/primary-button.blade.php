<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white dark:bg-[#C9A84C] dark:text-[#0B1829] dark:hover:bg-[#E7D59C] dark:focus:bg-[#E7D59C] dark:focus:ring-[#C9A84C] dark:focus:ring-offset-[#0F223C] transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
