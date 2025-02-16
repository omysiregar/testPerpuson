@extends('layouts.main')

@section('content')
    <div class="p-4 h-full">
        <div class="w-full bg-white rounded-lg p-3">
            <div class="flex">
                <a href="{{ route('books.index') }}" class="text-sm font-medium text-blue-500 flex items-center">
                    <i data-feather="arrow-left" class="w-5 h-5 text-sky-600"></i>
                    <span class="ml-2 text-sky-600">Detail Buku</span>
                </a>
            </div>
            <div class="grid grid-cols-4 gap-6 mt-3 relative">
                <div class="flex flex-col items-center sticky top-0">
                    <img src="{{ asset('storage/' . $book->image) }}" alt="{{ $book->title }}"
                        class="w-full h-96 object-cover rounded-lg shadow-lg">

                    @if ($pinjam->isNotEmpty())
                        <button type="submit" disabled
                            class="w-full mt-3 transition-all duration-500 enabled:bg-gradient-to-br enabled:from-blue-400 enabled:to-blue-600 rounded-lg text-white font-medium p-4 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-center hover:bg-blue-600 text-sm shadow-lg hover:shadow-xl shadow-blue-200 hover:shadow-blue-200 focus:shadow-none disabled:shadow-none disabled:bg-slate-700 disabled:cursor-not-allowed">Sedang
                            Meminjam</button>
                    @else
                        <button type="submit" onclick="openModal()"
                            class="w-full mt-3 transition-all duration-500 enabled:bg-gradient-to-br enabled:from-blue-400 enabled:to-blue-600 rounded-lg text-white font-medium p-4 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-center hover:bg-blue-600 text-sm shadow-lg hover:shadow-xl shadow-blue-200 hover:shadow-blue-200 focus:shadow-none disabled:shadow-none disabled:bg-slate-700 disabled:cursor-not-allowed">Pinjam</button>
                    @endif

                    {{-- <p class="text-slate-700 mt-2 text-sm font-medium">Jumlah Buku : {{ $book->stok }}</p> --}}
                </div>
                <div class="col-span-3">
                    <h1 class="text-3xl font-semibold text-slate-800">{{ $book->title }}</h1>
                    <h2 class="text-base font-medium mt-2 text-slate-700">
                        {{ $book->penulis }} - {{ $book->penerbit }}
                    </h2>
                    <div class="flex items-center">
                        <div class="flex relative @if ($book->histories->isNotEmpty()) ml-2 @endif mt-2 items-center">
                            @if ($book->histories)
                                @if ($book->histories->isNotEmpty())
                                    @foreach ($book->histories->unique('user_id') as $history)
                                        <img src="{{ asset('storage/' . $history->user->image) }}"
                                            alt="{{ $history->user->name }}"
                                            class="w-10 h-10 overflow-hidden rounded-full object-cover border border-white relative -ml-3">
                                    @endforeach
                                @endif
                                <span
                                    class="text-slate-700 @if ($book->histories->isNotEmpty()) ml-2 @else mr-2 @endif">{{ $book->histories->count() }}
                                    people have read</span>
                            @endif
                        </div>
                        <div class="flex mt-2 @if ($book->histories->isNotEmpty()) ml-2 @endif">
                            <h2 class="text-slate-700">| Kategori :</h2>
                            <a href="{{ route('category.show', $book->category->slug) }}"
                                class="text-slate-700 ml-1 underline decoration-double decoration-blue-600">{{ $book->category->name }}</a>
                        </div>
                    </div>

                    @if ($book->borrow->isNotEmpty() && $book->borrow->where('status', 'meminjam')->isNotEmpty())
                        <p class="text-base text-slate-700 mt-2">{{ $book->description }}</p>
                    @elseif ($book->borrow->where('status', 'meminjam')->isEmpty())
                        <p class="text-base text-slate-700 mt-2">{{ Str::words($book->description, 20, '...') }}</p><u
                            class="text-base text-blue-800 mt-2"> Silahkan lakukan peminjaman untuk membaca</u>
                    @else
                        <p class="text-base text-slate-700 mt-2">{{ Str::words($book->description, 20, '...') }}</p><u
                            class="text-base text-blue-800 mt-2"> Silahkan lakukan peminjaman untuk membaca</u>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div id="modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white w-full max-w-lg rounded-lg shadow-lg p-6 relative">
            <button onclick="closeModal()"
                class="absolute top-2 right-2 text-gray-600 hover:text-red-500 text-2xl">&times;</button>
            <h2 class="text-xl font-bold text-gray-800 mb-4">Form Peminjaman</h2>
            <form action="{{ route('borrow.store') }}" method="post" class="w-full">
                @csrf
                <input type="text" name="user_id" value="{{ auth()->user()->id }}" hidden>
                <input type="text" name="book_id" value="{{ $book->id }}" hidden>
                <input type="text" name="kode_peminjaman"
                    value="{{ now()->format('dHisv') . auth()->user()->id . $book->kode_buku }}" hidden>
                <input type="text" name="status" value="meminjam" hidden>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold">Nama Peminjam</label>
                    <input type="text" value="{{ auth()->user()->name }}" readonly
                        class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-blue-300 outline-none">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold">Rencana Pengembalian</label>
                    <input type="text" id="datepicker" name="tgl_return"
                        class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-blue-300 outline-none">
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 text-gray-600 hover:text-red-500">Cancel</button>
                    <button type="submit"
                        class="px-6 py-2 bg-blue-500 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        function openModal() {
            document.getElementById("modal").classList.remove("hidden");
        }

        function closeModal() {
            document.getElementById("modal").classList.add("hidden");
        }
        flatpickr("#datepicker", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "F j, Y",
            minDate: "today", //hari ini
            maxDate: new Date().fp_incr(30) // Maksimal 30 hari
        });
        // flatpickr("#datepicker", {
        //     dateFormat: "Y-m-d",
        //     altInput: true,
        //     altFormat: "F j, Y",
        // });
    </script>
@endsection
