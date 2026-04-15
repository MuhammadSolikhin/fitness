<div class="form-group">
    <label for="name">Nama Kelas</label>
    <input type="text" name="name" id="name" class="form-control"
        value="{{ old('name', isset($class) ? $class->name : '') }}" required>
</div>

<div class="form-group">
    <label for="sanggar_name">Nama Sanggar</label>
    <select name="sanggar_name" id="sanggar_name" class="form-control" required>
        <option value="sanggar-1" {{ old('sanggar_name', isset($class) ? $class->sanggar_name : '') == 'sanggar-1' ? 'selected' : '' }}>
            Sanggar Kurnia 1
        </option>
        <option value="sanggar-2" {{ old('sanggar_name', isset($class) ? $class->sanggar_name : '') == 'sanggar-2' ? 'selected' : '' }}>
            Sanggar Kurnia 2
        </option>
        <option value="sanggar-3" {{ old('sanggar_name', isset($class) ? $class->sanggar_name : '') == 'sanggar-3' ? 'selected' : '' }}>
            Sanggar Kurnia 3
        </option>
    </select>
</div>


<div class="form-group">
    <label for="image">Gambar Kelas</label>
    <input type="file" name="image" id="image" class="form-control-file">
</div>

<div class="form-group">
    <label for="description">Deskripsi</label>
    <textarea name="description" id="description"
        class="form-control">{{ old('description', isset($class) ? $class->description : '') }}</textarea>
</div>

<div class="form-group">
    <label for="coach_id">Pelatih</label>
    <select name="coach_id" id="coach_id" class="form-control" required>
        <option value="">-- Pilih Pelatih --</option>
        @foreach ($coaches as $coach)
            <option value="{{ $coach->id }}" {{ old('coach_id', isset($class) ? $class->coach_id : '') == $coach->id ? 'selected' : '' }}>
                {{ $coach->name }}
            </option>
        @endforeach
    </select>
</div>