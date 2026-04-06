@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 bg-white text-gray-900 placeholder:text-gray-400 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-slate-500 dark:bg-slate-700 dark:text-white dark:placeholder:text-slate-400 dark:focus:border-[#C9A84C] dark:focus:ring-[#C9A84C]']) }}>
