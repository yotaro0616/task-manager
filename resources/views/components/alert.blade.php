@props(['type' => 'success'])

@if (session($type))
    <div class="alert alert-{{ $type }}"
        style="padding: 15px; margin-bottom: 15px; border-radius: 4px; {{ $type === 'success' ? 'background-color: #d4edda; color: #155724;' : 'background-color: #f8d7da; color: #721c24;' }}">
        {{ session($type) }}
    </div>
@endif
