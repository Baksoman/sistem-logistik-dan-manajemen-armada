@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <div class="w-full lg:max-w-5xl md:max-w-2xl sm:max-w-lg mx-auto px-4 sm:px-6 py-8 relative z-10 flex-1 flex flex-col justify-center">
        
        <!-- Deep inset well to make the card pop even more -->
        <div class="bg-gray-100 rounded-[3rem] shadow-[inset_8px_8px_16px_#d1d5db,inset_-8px_-8px_16px_#ffffff] p-3 md:p-4">
            
            <!-- Raised Card inside the well (Split View Container) -->
            <div class="bg-gray-100 rounded-[2.5rem] shadow-[8px_8px_16px_#d1d5db,-8px_-8px_16px_#ffffff] relative overflow-hidden flex flex-col lg:flex-row">
                
                <!-- LEFT SIDE: Login Form (50%) -->
                <div class="w-full lg:w-1/2 p-8 md:p-12 relative z-10 flex flex-col justify-center border-b-2 lg:border-b-0 lg:border-r-2 border-white/60">
                    
                    <div class="mb-10 text-center relative z-10">
                        <div class="w-16 h-16 mx-auto rounded-full shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff] flex items-center justify-center text-gray-700 mb-6 bg-gray-100 border-4 border-gray-100 lg:hidden">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <h2 class="text-3xl font-extrabold text-gray-800 tracking-tight">Sign In</h2>
                        <p class="text-gray-500 mt-2 font-medium">Access your logistics dashboard</p>
                    </div>

                    <form method="POST" action="{{ route('login') ?? '#' }}" class="relative z-10 w-full max-w-sm mx-auto">
                        @csrf

                        <!-- Email Address -->
                        <div class="mb-6">
                            <label for="email" class="block font-bold text-xs text-gray-400 uppercase tracking-widest mb-3 ml-2 transition-colors duration-300 group-focus-within:text-gray-900">Email Address</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none transition-colors duration-300 text-gray-400 group-focus-within:text-gray-900">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
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
                            <label for="password" class="block font-bold text-xs text-gray-400 uppercase tracking-widest mb-3 ml-2 transition-colors duration-300 group-focus-within:text-gray-900">Password</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none transition-colors duration-300 text-gray-400 group-focus-within:text-gray-900">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"></path></svg>
                                </div>
                                <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" 
                                    class="w-full bg-gray-100 border-none rounded-2xl pl-12 pr-5 py-4 text-gray-700 font-medium tracking-widest shadow-[inset_5px_5px_10px_#d1d5db,inset_-5px_-5px_10px_#ffffff] focus:outline-none focus:ring-0 focus:shadow-[inset_3px_3px_6px_#d1d5db,inset_-3px_-3px_6px_#ffffff] transition-all duration-300 placeholder-gray-400" />
                            </div>
                            @error('password')
                                <p class="text-red-500 text-xs font-bold mt-2 ml-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between gap-5 mb-10" x-data="{ remember: false }">
                            <label class="flex items-center cursor-pointer group">
                                <div class="relative">
                                    <!-- Track -->
                                    <div class="w-11 h-6 bg-gray-100 rounded-full shadow-[inset_3px_3px_6px_#d1d5db,inset_-3px_-3px_6px_#ffffff] transition-all duration-300"></div>
                                    <!-- Thumb -->
                                    <div class="absolute left-1 top-1 w-4 h-4 rounded-full shadow-[2px_2px_4px_#d1d5db,-2px_-2px_4px_#ffffff] transition-all duration-300 transform"
                                         :class="remember ? 'translate-x-2.5 bg-gray-900 shadow-none' : 'translate-x-0 bg-gray-100'"></div>
                                </div>
                                <span class="ml-3 text-xs font-bold transition-colors duration-300" :class="remember ? 'text-gray-700' : 'text-gray-400'">Remember me</span>
                                <input id="remember_me" type="checkbox" name="remember" class="hidden" x-model="remember">
                            </label>

                            <a class="text-xs font-bold text-gray-400 hover:text-gray-800 transition-colors" href="#">
                                Forgot Password?
                            </a>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="w-full flex items-center justify-center gap-3 px-8 py-4 bg-gray-100 border border-transparent rounded-2xl font-extrabold text-gray-900 uppercase tracking-widest transition-all duration-300 ease-out shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff] hover:shadow-[10px_10px_20px_#c2c6cc,-10px_-10px_20px_#ffffff] hover:-translate-y-1 hover:text-black active:shadow-[inset_5px_5px_10px_#d1d5db,inset_-5px_-5px_10px_#ffffff] active:translate-y-0 active:scale-95 focus:outline-none">
                            <span>Log in</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>

                        <!-- Social Login Divider -->
                        <div class="mt-8">
                            <div class="relative">
                                <div class="absolute inset-0 flex items-center">
                                    <div class="w-full border-t border-gray-300 shadow-[0_1px_0_#ffffff]"></div>
                                </div>
                                <div class="relative flex justify-center text-sm">
                                    <span class="px-4 bg-gray-100 text-gray-400 font-bold tracking-widest uppercase text-xs">Or sign in with</span>
                                </div>
                            </div>

                            <!-- Social Login Buttons -->
                            <div class="mt-6 flex justify-center gap-6">
                                <!-- Google Button (Monochrome Neumorphic) -->
                                <a href="{{ route('auth.google') }}" class="w-12 h-12 rounded-full flex items-center justify-center bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[6px_6px_12px_#c2c6cc,-6px_-6px_12px_#ffffff] hover:-translate-y-0.5 active:shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] active:translate-y-0 transition-all text-gray-500 hover:text-gray-900">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
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

                <!-- RIGHT SIDE: Showcase & Decor (50%) -->
                <div class="w-full lg:w-1/2 p-10 md:p-14 relative flex flex-col items-center justify-center bg-gray-100 overflow-hidden">
                    
                    <!-- Text Content -->
                    <div class="text-center mb-16 relative z-20">
                        <h2 class="text-4xl font-extrabold text-gray-800 tracking-tight mb-5">Welcome to <span class="text-blue-600">LogiX</span></h2>
                        <p class="text-gray-500 font-medium leading-relaxed max-w-sm mx-auto">
                            Streamline your supply chain. Manage fleet tracking, warehouse inventory, and order shipments seamlessly in one unified platform.
                        </p>
                    </div>

                    <!-- Pure Neumorphism Art (Bunder-bunder) -->
                    <div class="relative w-64 h-64 flex items-center justify-center rounded-full bg-gray-100 shadow-[16px_16px_32px_#d1d5db,-16px_-16px_32px_#ffffff] z-10 group transition-transform duration-[800ms] ease-out hover:scale-105">
                        
                        <!-- Inner inset circle -->
                        <div class="w-44 h-44 rounded-full bg-gray-100 shadow-[inset_12px_12px_24px_#c8cdd3,inset_-12px_-12px_24px_#ffffff] flex items-center justify-center transition-all duration-700 ease-out group-hover:shadow-[inset_16px_16px_32px_#c8cdd3,inset_-16px_-16px_32px_#ffffff]">
                            
                            <!-- Center extruded circle -->
                            <div class="w-20 h-20 rounded-full bg-gray-100 shadow-[8px_8px_16px_#c8cdd3,-8px_-8px_16px_#ffffff] text-blue-600 flex items-center justify-center transition-all duration-500 ease-out group-hover:shadow-[4px_4px_8px_#c8cdd3,-4px_-4px_8px_#ffffff]">
                                <svg class="w-10 h-10 transform group-hover:rotate-12 group-hover:scale-110 transition-all duration-500 ease-out" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Floating Orb 1 (Top Right) -->
                        <div class="absolute -top-4 -right-8 w-16 h-16 rounded-full bg-gray-100 shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff] animate-[bounce_6s_infinite]"></div>
                        
                        <!-- Floating Orb 2 (Bottom Left) -->
                        <div class="absolute -bottom-6 -left-4 w-12 h-12 rounded-full bg-gray-100 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] animate-[bounce_8s_infinite_reverse]"></div>
                        
                        <!-- Floating Orb 3 (Top Left) -->
                        <div class="absolute top-10 -left-12 w-8 h-8 rounded-full bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] animate-[pulse_4s_infinite]"></div>
                    </div>

                    <!-- Decorative background subtle glow (optional to give a hint of color) -->
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[120%] h-[120%] bg-gradient-to-tr from-blue-100/30 to-transparent rounded-full blur-3xl opacity-50 z-0 pointer-events-none"></div>
                </div>

            </div>
        </div>
    </div>
@endsection
