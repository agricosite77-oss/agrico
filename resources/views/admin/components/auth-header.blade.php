@props([
    'title',
    'description'
])


<div class="flex flex-col gap-2 text-center">


    <h1
        class="
        text-2xl
        font-bold
        text-gray-900
        dark:text-white
        "
    >
        {{ $title }}
    </h1>



    <p
        class="
        text-sm
        text-gray-500
        dark:text-gray-400
        "
    >
        {{ $description }}
    </p>


</div>
