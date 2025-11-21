@extends('layouts.app')

@section('header', 'Manajemen Edukasi')

@section('content')

{{-- success message --}}
@if (session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-sm">
        {{ session('success') }}
    </div>
@endif

{{-- header --}}
<div class="flex justify-end mb-4">
    <a href="{{ route('admin.edukasi.create') }}"
       class="px-4 py-2 bg-gold-500 text-white rounded-xl shadow hover:bg-gold-600 transition-all font-semibold">
        + Tulis Artikel Baru
    </a>
</div>

{{-- table --}}
<div class="bg-white shadow-md rounded-xl overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs text-gray-500 uppercase">Judul</th>
                <th class="px-6 py-3 text-left text-xs text-gray-500 uppercase">Penulis</th>
                <th class="px-6 py-3 text-left text-xs text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($edukasi as $artikel)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 max-w-sm break-words">
                        <div class="flex items-center gap-3">
                            @if($artikel->gambar_fitur)
                                <button type="button" onclick="lihatGambar('{{ asset('storage/' . $artikel->gambar_fitur) }}', '{{ $artikel->judul }}')">
                                    <img src="{{ asset('storage/' . $artikel->gambar_fitur) }}" alt="{{ $artikel->judul }}" class="h-10 w-16 object-cover rounded-md border border-gray-200 hover:scale-110 transition-all">
                                </button>
                            @else
                                <div class="h-10 w-16 bg-gray-100 rounded-md flex items-center justify-center text-gray-400 flex-shrink-0">
                                    <i class="fa-solid fa-image"></i>
                                </div>
                            @endif
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $artikel->judul }}</p>
                                <p class="text-xs text-gray-500">Dibuat: {{ $artikel->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $artikel->author->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-xl
                            @if($artikel->status=='Published') bg-green-100 text-green-800 @endif
                            @if($artikel->status=='Draft') bg-gray-100 text-gray-800 @endif">
                            {{ $artikel->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right text-sm space-x-3 whitespace-nowrap">
                        <a href="{{ route('admin.edukasi.edit', $artikel) }}"
                           class="text-indigo-600 hover:text-indigo-900 transition font-medium">
                            Edit
                        </a>

                        <button type="button" 
                                onclick="siapkanHapusEdukasi('{{ route('admin.edukasi.destroy', $artikel) }}', '{{ $artikel->judul }}')"
                                class="text-red-600 hover:text-red-800 transition font-medium">
                            Hapus
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                        Belum ada artikel edukasi yang ditulis.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $edukasi->links() }}
</div>

{{-- Modal Hapus (Tetap digunakan) --}}
<div id="modalHapusEdukasi" class="modal hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex justify-center items-center z-50">
    <div class="modal-content bg-white w-full max-w-md rounded-2xl shadow-2xl p-6 scale-90 opacity-0 transition-all duration-300">
        <div class="flex items-center">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                <i class="fa-solid fa-triangle-exclamation text-red-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-xl font-bold mb-1 text-gray-900">Konfirmasi Hapus</h2>
                <p class="text-sm text-gray-600">Anda yakin ingin menghapus artikel ini?</p>
            </div>
        </div>
        <div class="mt-4 bg-gray-50 p-3 rounded-lg">
            <p class="text-sm text-gray-700">Artikel: <strong id="itemHapusNamaEdukasi" class="font-semibold">...</strong></p>
        </div>
        <form id="formHapusEdukasi" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex justify-end mt-6 space-x-2">
                <button type="button" onclick="closeModal('modalHapusEdukasi')" class="px-4 py-2 bg-gray-200 rounded-xl hover:bg-gray-300 transition font-semibold text-gray-700">Batal</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition font-semibold">Ya, Hapus Artikel</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Lihat Gambar --}}
<div id="modalLihatGambar" class="modal hidden fixed inset-0 bg-black/80 flex justify-center items-center z-50 p-4" onclick="closeModal('modalLihatGambar')">
    <button type="button" onclick="closeModal('modalLihatGambar')" class="absolute top-4 right-4 text-white/70 hover:text-white transition-all z-[60]">
        <i class="fa-solid fa-times text-4xl"></i>
    </button>
    <div class="modal-content scale-90 opacity-0 transition-all duration-300 relative" onclick="event.stopPropagation()">
        <img id="lihatGambarSrc" src="" alt="Preview Gambar" 
             class="w-auto h-auto object-contain rounded-lg shadow-lg" 
             style="max-width: 90vw; max-height: 85vh;">
        <div class="mt-3 text-center">
            <h3 id="lihatGambarJudul" class="text-lg font-semibold text-white">...</h3>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Fungsi Hapus
    function siapkanHapusEdukasi(url, nama) {
        document.getElementById('formHapusEdukasi').action = url;
        document.getElementById('itemHapusNamaEdukasi').textContent = nama;
        openModal('modalHapusEdukasi');
    }

    // Fungsi Lihat Gambar
    function lihatGambar(url, judul) {
        document.getElementById('lihatGambarSrc').src = url;
        document.getElementById('lihatGambarJudul').textContent = judul;
        openModal('modalLihatGambar');
    }
</script>
@endsection