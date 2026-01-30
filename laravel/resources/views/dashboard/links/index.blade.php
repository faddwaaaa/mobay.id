@extends('layouts.dashboard')

@section('content')
<div class="max-w-5xl mx-auto">

    <!-- HEADER -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Link Saya</h1>
        <p class="text-gray-600">
            Link Saya: 
            <a href="https://lynk.id/lin_" class="text-blue-600 hover:text-blue-800 font-medium">
                https://lynk.id/lin_
            </a>
        </p>
    </div>

    <!-- DUA KOLOM LAYOUT -->
    <div class="flex flex-col lg:flex-row gap-6">
        
        <!-- KOLOM KIRI - DAFTAR PAGE -->
        <div class="lg:w-2/3">
            <div class="bg-white rounded-xl shadow mb-6">
                <!-- HEADER YOUR PAGES -->
                <div class="px-6 py-4 border-b">
                    <h2 class="font-bold text-gray-900">Your Pages</h2>
                </div>

                <!-- PAGE HOME -->
                <div class="px-6 py-4 border-b flex items-center justify-between hover:bg-gray-50">
                    <div>
                        <h3 class="font-medium text-gray-900">Home</h3>
                    </div>
                    <div class="flex items-center gap-3">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            <span class="ml-2 text-sm text-gray-600">Show</span>
                        </label>
                        
                        <button class="text-gray-500 hover:text-blue-600 p-1">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                            </svg>
                        </button>
                        
                        <button class="text-gray-500 hover:text-red-600 p-1">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- ADD NEW PAGE -->
                <div class="px-6 py-4">
                    <button class="flex items-center text-blue-600 hover:text-blue-800 font-medium">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"/>
                        </svg>
                        Add new page
                    </button>
                </div>
            </div>

            <!-- BLOCK LIST -->
            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b">
                    <h2 class="font-bold text-gray-900">Block List</h2>
                </div>
                
                <!-- ADD NEW BLOCK -->
                <div class="p-6">
                    <div class="mb-4">
                        <h3 class="font-medium text-gray-900 mb-3">Add new block</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <!-- Image Block -->
                            <button class="border border-gray-200 rounded-lg p-4 hover:border-blue-400 hover:bg-blue-50 transition">
                                <div class="flex flex-col items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mb-2">
                                        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Image</span>
                                </div>
                            </button>
                            
                            <!-- Link Block -->
                            <button class="border border-gray-200 rounded-lg p-4 hover:border-blue-400 hover:bg-blue-50 transition">
                                <div class="flex flex-col items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mb-2">
                                        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Link</span>
                                </div>
                            </button>
                            
                            <!-- Text Block -->
                            <button class="border border-gray-200 rounded-lg p-4 hover:border-blue-400 hover:bg-blue-50 transition">
                                <div class="flex flex-col items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mb-2">
                                        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Text</span>
                                </div>
                            </button>
                            
                            <!-- Video Block -->
                            <button class="border border-gray-200 rounded-lg p-4 hover:border-blue-400 hover:bg-blue-50 transition">
                                <div class="flex flex-col items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mb-2">
                                        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Video</span>
                                </div>
                            </button>
                        </div>
                    </div>
                    
                    <!-- BLOCK KOSONG -->
                    <div class="text-center py-8 text-gray-400 border border-dashed border-gray-300 rounded-lg">
                        <p class="mb-2">Belum ada block</p>
                        <p class="text-sm">Tambahkan block untuk menampilkan konten di halaman kamu</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN - PAGE PREVIEW -->


        <!-- PHONE FRAME -->
        <div class="mx-auto w-[280px] h-[560px] rounded-[36px] border-[10px] border-gray-900 shadow-xl bg-black overflow-hidden">

            <!-- NOTCH -->
            <div class="h-6 bg-black flex justify-center items-end">
                <div class="w-20 h-1.5 bg-gray-700 rounded-full mb-1"></div>
            </div>

            <!-- IFRAME -->
            <iframe
                src="{{ url('/preview/'.$user->username) }}"
                class="w-full h-full bg-white"
                frameborder="0">
            </iframe>

        </div>

    </div>
</div>

            </div>
        </div>
    </div>
</div>

<!-- STYLE TAMBAHAN -->
<style>
    .toggle-checkbox:checked {
        right: 0;
        border-color: #2563eb;
    }
    .toggle-checkbox:checked + .toggle-label {
        background-color: #2563eb;
    }
</style>
@endsection