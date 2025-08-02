<template id="form1">
    <div class="relative py-12 md:py-16 mt-10 max-w-lg mx-auto bg-white/50 p-1 rounded-xl backdrop-blur-md">
        <div class="w-full h-full px-2">
            <form
                id="modal-form"
                class="modal-form px-6 h-full overflow-y-auto scrollbar-hide md:px-16 rounded-[30px] text-white space-y-5 ">
                <!-- Заголовок -->
                <h2 class="text-2xl text-center">Обратный звонок</h2>

                <div
                    class="relative rounded-full">
                    <input type="text" placeholder="*Ваше имя" data-required name="name"
                           class="w-full rounded-full px-6 py-3 bg-white/80 placeholder-gray-400 text-gray-600 outline-none focus:ring-2 focus:ring-white/30 transition"/>
                </div>
                <div
                    class="relative rounded-full">
                    <input type="tel" placeholder="*Номер телефона" data-required name="phone"
                           class="phone-input w-full rounded-full px-6 py-3 bg-white/80 placeholder-gray-400 text-gray-600 outline-none focus:ring-2 focus:ring-white/30 transition"/>
                </div>
                <div
                    class="relative rounded-full">
                    <input type="email" placeholder="Email" name="email"
                           class="w-full rounded-full px-6 py-3 bg-white/80 placeholder-gray-400 text-gray-600 outline-none focus:ring-2 focus:ring-white/30 transition"/>
                </div>
                <div
                    class="relative rounded-full">
                    <textarea placeholder="Сообщение" name="message" class="w-full rounded-full px-6 py-3 bg-white/80 placeholder-gray-400 text-gray-600 outline-none focus:ring-2 focus:ring-white/30 transition"></textarea>
                </div>
                <!-- Alert -->
                <div class="form-alert hidden text-sm mt-2"></div>

                <!-- Loader -->
                <div class="form-loader hidden absolute inset-0 bg-white/60 flex items-center justify-center z-10">
                    <span class="animate-spin h-6 w-6 border-4 border-gray-300 border-t-blue-500 rounded-full"></span>
                </div>

                <!-- Кнопка -->
                <button type="submit"
                        class="bg-primary hover:bg-primary-dark text-white-custom px-6 py-3 rounded-full w-full font-medium transition-all hover:-translate-y-0.5">
                    отправить заявку
                </button>

                <!-- Checkbox -->
                <label class="flex items-start space-x-3 text-[10px] mt-4 leading-snug text-white">
                    <input
                        type="checkbox"
                        class="peer sr-only"
                        name="agree"
                        required
                        checked
                    />
                    <div
                        class="w-5 h-5 border border-white rounded-md flex-shrink-0 relative flex items-center justify-center transition-all duration-200 peer-checked:[&_svg]:opacity-100"
                    >
                        <!-- Галочка -->
                        <svg width="13" height="10" viewBox="0 0 13 10" fill="none" xmlns="http://www.w3.org/2000/svg"
                             class="absolute w-3 h-3 text-black opacity-0  transition-opacity duration-200">
                            <path d="M1 4.99998L4.53553 8.53551L11.6058 1.46442" stroke="white" stroke-width="2"
                                  stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>

                    <span>
                    Я даю согласие на обработку персональных данных и соглашаюсь с <a href="/policy" target="_blank" class="underline">политикой конфиденциальности</a>
                  </span>
                </label>

            </form>
        </div>
    </div>

</template>
