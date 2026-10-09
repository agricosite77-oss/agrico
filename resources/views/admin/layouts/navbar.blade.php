<header
    class="
h-20
bg-white
border-b
border-gray-100
px-6
lg:px-8
flex
items-center
justify-between
">


    {{-- Left Section --}}

    <div>


        <h1
            class="
text-xl
font-extrabold
text-gray-900
">

            Admin Dashboard

        </h1>


        <p
            class="
text-xs
text-gray-400
">

            Manage AgriCo system

        </p>


    </div>









    {{-- Right Section --}}

    <div
        class="
flex
items-center
gap-4
">





        {{-- Search --}}

        <div
            class="
hidden
md:flex
items-center
bg-gray-50
rounded-xl
px-4
py-2
w-72
">


            <svg
                class="
w-5
h-5
text-gray-400
mr-3
"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1010.5 3a7.5 7.5 0 006.15 13.65z" />

            </svg>



            <input
                type="text"
                placeholder="Search..."
                class="
bg-transparent
border-none
outline-none
text-sm
w-full
" />


        </div>








        {{-- Notification --}}

        <button
            class="
relative
w-11
h-11
rounded-xl
bg-gray-50
flex
items-center
justify-center
hover:bg-green-50
">


            <svg
                class="
w-5
h-5
text-gray-500
"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />

            </svg>



            <span
                class="
absolute
top-2
right-2
w-2
h-2
bg-green-500
rounded-full
">
            </span>


        </button>









        {{-- User Profile --}}

        <div
            class="
flex
items-center
gap-3
">


            <div
                class="
w-10
h-10
rounded-full
bg-green-600
text-white
flex
items-center
justify-center
font-bold
">

                {{ auth()->user()?->initials() }}

            </div>



            <div
                class="
hidden
lg:block
">

                <p
                    class="
font-bold
text-sm
text-gray-800
">

                    {{ auth()->user()?->name }}

                </p>


                <p
                    class="
text-xs
text-gray-400
">

                    Administrator

                </p>


            </div>



        </div>



    </div>



</header>
