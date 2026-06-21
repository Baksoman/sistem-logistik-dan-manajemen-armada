@extends('layouts.guest')

@section('title', 'Login')


@section('content')
    <div class="w-full sm:max-w-lg my-auto px-6 py-12 relative z-10">
        
        <!-- Deep inset well to make the card pop even more -->
        <div class="bg-gray-100 rounded-[2.5rem] shadow-[inset_8px_8px_16px_#d1d5db,inset_-8px_-8px_16px_#ffffff] p-3 md:p-4">
            
            <!-- Raised Card inside the well -->
            <div class="bg-gray-100 rounded-[2rem] shadow-[8px_8px_16px_#d1d5db,-8px_-8px_16px_#ffffff] p-8 md:p-10 relative overflow-hidden">
                
                <!-- Decorative background lighting elements inside the card -->
                <div class="absolute top-0 right-0 w-32 h-32 bg-gray-200 rounded-full mix-blend-multiply filter blur-2xl opacity-50 -translate-y-10 translate-x-10 pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 w-32 h-32 bg-white rounded-full mix-blend-overlay filter blur-2xl opacity-60 translate-y-10 -translate-x-10 pointer-events-none"></div>

                <div class="mb-10 text-center relative z-10">
                    <div class="w-20 h-20 mx-auto rounded-full shadow-[8px_8px_16px_#d1d5db,-8px_-8px_16px_#ffffff] flex items-center justify-center text-gray-700 mb-6 bg-gray-100 border-4 border-gray-100">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <h2 class="text-3xl font-extrabold text-gray-800 tracking-tight">Sistem Logistik</h2>
                    <p class="text-gray-500 mt-2 font-medium">Manajemen Armada & Pengiriman</p>
                </div>

                <form method="POST" action="{{ route('login') ?? '#' }}" class="relative z-10">
                    @csrf

                    <!-- Email Address -->
                    <div class="mb-6">
                        <label for="email" class="block font-bold text-xs text-gray-400 uppercase tracking-widest mb-3 ml-2">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                            </div>
                            <input id="email" type="email" name="email" :value="old('email')" required autofocus placeholder="name@logistik.app" 
                                class="w-full bg-gray-100 border-none rounded-2xl pl-12 pr-5 py-4 text-gray-700 font-medium shadow-[inset_5px_5px_10px_#d1d5db,inset_-5px_-5px_10px_#ffffff] focus:outline-none focus:ring-0 focus:shadow-[inset_3px_3px_6px_#d1d5db,inset_-3px_-3px_6px_#ffffff] transition-all duration-300 placeholder-gray-400" />
                        </div>
                        @error('email')
                            <p class="text-red-500 text-xs font-bold mt-2 ml-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-8">
                        <label for="password" class="block font-bold text-xs text-gray-400 uppercase tracking-widest mb-3 ml-2">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"></path></svg>
                            </div>
                            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" 
                                class="w-full bg-gray-100 border-none rounded-2xl pl-12 pr-5 py-4 text-gray-700 font-medium tracking-widest shadow-[inset_5px_5px_10px_#d1d5db,inset_-5px_-5px_10px_#ffffff] focus:outline-none focus:ring-0 focus:shadow-[inset_3px_3px_6px_#d1d5db,inset_-3px_-3px_6px_#ffffff] transition-all duration-300 placeholder-gray-400" />
                        </div>
                        @error('password')
                            <p class="text-red-500 text-xs font-bold mt-2 ml-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me & Forgot Password (Responsive) -->
                    <div class="flex flex-col sm:flex-row items-center sm:justify-between gap-5 sm:gap-0 mb-10" x-data="{ remember: false }">
                        <label class="flex items-center cursor-pointer group w-full sm:w-auto justify-center sm:justify-start">
                            <div class="relative">
                                <!-- Track -->
                                <div class="w-11 h-6 bg-gray-100 rounded-full shadow-[inset_3px_3px_6px_#d1d5db,inset_-3px_-3px_6px_#ffffff] transition-all duration-300"></div>
                                <!-- Thumb -->
                                <div class="absolute left-1 top-1 w-4 h-4 rounded-full shadow-[2px_2px_4px_#d1d5db,-2px_-2px_4px_#ffffff] transition-all duration-300 transform"
                                     :class="remember ? 'translate-x-2.5 bg-gray-600 shadow-none' : 'translate-x-0 bg-gray-100'"></div>
                            </div>
                            <span class="ml-3 text-sm font-bold transition-colors duration-300" :class="remember ? 'text-gray-700' : 'text-gray-400'">Remember me</span>
                            <input id="remember_me" type="checkbox" name="remember" class="hidden" x-model="remember">
                        </label>

                        <a class="text-sm font-bold text-gray-400 hover:text-gray-800 transition-colors w-full sm:w-auto text-center sm:text-right" href="#">
                            Forgot Password?
                        </a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full flex items-center justify-center gap-3 px-8 py-4 bg-gray-100 border border-transparent rounded-2xl font-extrabold text-gray-600 uppercase tracking-widest transition-all duration-300 ease-out shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff] hover:shadow-[10px_10px_20px_#c2c6cc,-10px_-10px_20px_#ffffff] hover:-translate-y-1 hover:text-gray-900 active:shadow-[inset_5px_5px_10px_#d1d5db,inset_-5px_-5px_10px_#ffffff] active:translate-y-0 active:scale-95 focus:outline-none">
                        <span>Log in</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>

                    <!-- Social Login Divider -->
                    <div class="mt-10">
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-300 shadow-[0_1px_0_#ffffff]"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-4 bg-gray-100 text-gray-400 font-bold tracking-widest uppercase text-xs">Or sign in with</span>
                            </div>
                        </div>

                        <!-- Social Login Buttons -->
                        <div class="mt-8 flex justify-center gap-6">
                            <!-- Google Button (Monochrome) -->
                            <a href="#" class="w-14 h-14 rounded-full flex items-center justify-center bg-gray-100 shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff] hover:shadow-[8px_8px_16px_#c2c6cc,-8px_-8px_16px_#ffffff] hover:-translate-y-1 active:shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] active:translate-y-0 transition-all text-gray-600 hover:text-gray-900">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
