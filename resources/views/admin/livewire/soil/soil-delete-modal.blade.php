<div>
@if($showModal)

<div
    class="
fixed
inset-0
z-50
flex
items-center
justify-center
"
    style="background: rgba(0,0,0,0.18); backdrop-filter: blur(25px);"
>


    <div
        class="
bg-white
rounded-2xl
shadow-2xl
w-full
max-w-sm
px-6
py-6
mx-4
"
    >


        {{-- CONTENT --}}

        <div class="flex gap-4">


            {{-- WARNING ICON --}}

            <div
                class="
w-11
h-11
rounded-full
bg-red-50
flex
items-center
justify-center
shrink-0
"
            >

                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>

            </div>



            <div class="flex-1">


                <h2
                    class="
text-base
font-bold
text-gray-800
"
                >

                    Delete Soil Record

                </h2>


                <p
                    class="
text-sm
text-gray-500
leading-relaxed
mt-1
"
                >

                    This will permanently remove the soil record
                    and all associated data. This action cannot be undone.

                </p>


            </div>



            <x-close-button wire:click="closeModal" />


        </div>





        {{-- BUTTONS --}}

        <div class="flex gap-3 mt-5">


            <button

                wire:click="closeModal"

                class="
flex-1
py-3
rounded-full
border
border-gray-200
text-gray-600
font-bold
text-sm
hover:bg-gray-50
transition
"

            >

                Cancel

            </button>




            <button

                wire:click="delete"

                class="
flex-1
py-3
rounded-full
bg-red-500
hover:bg-red-600
text-white
font-bold
text-sm
shadow-md
shadow-red-500/35
transition
"

            >

                Delete Record

            </button>


        </div>




    </div>


</div>

@endif
</div>
