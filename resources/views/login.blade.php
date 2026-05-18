<!DOCTYPE html>
<html class="dark" lang="es">

<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<meta name="csrf-token" content="{{ csrf_token() }}"/>
<title>Login - Course Management System</title>

<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=JetBrains+Mono:wght@400&display=swap" rel="stylesheet"/>

<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
@if (file_exists(public_path('build/manifest.json')))
@vite(['resources/js/app.js'])
@endif

<style type="text/tailwindcss">
@layer base {
body{
font-family:'Inter',sans-serif;
background-color:#0f1115;
}
}

:root{
--primary-color:#f97316;
--surface-color:#1a1d23;
}

.minimal-input{
background-color:transparent;
border:1px solid rgba(255,255,255,0.1);
transition:all .2s ease;
}

.minimal-input:focus{
border-color:var(--primary-color);
outline:none;
}
</style>

<script>
tailwind.config={
darkMode:"class",
theme:{
extend:{
colors:{
primary:"#f97316",
surface:"#1a1d23",
},
fontFamily:{
sans:["Inter","sans-serif"],
mono:["JetBrains Mono","monospace"],
}
}
}
}
</script>

</head>

<body class="h-screen flex flex-col items-center justify-center p-6">

<div class="mb-12 text-center">
<h2 class="text-white/40 text-[11px] font-medium tracking-[0.5em] uppercase">
Registro de documentación de cursos
</h2>
</div>

<div class="w-full max-w-[420px] bg-surface border border-white/[0.08] rounded-xl p-10 shadow-2xl">

<div class="flex flex-col items-center mb-8">
<div class="w-16 h-16 rounded-full border border-white/[0.1] flex items-center justify-center mb-6 bg-white/[0.02]">
<span class="material-symbols-outlined text-slate-400 text-3xl">person</span>
</div>

<h1 class="text-white text-[13px] font-semibold tracking-[0.3em] uppercase">
Iniciar Sesión
</h1>
</div>


@if ($errors->any())
<div id="login-error" class="mb-6 text-red-400 text-sm">
{{$errors->first()}}
</div>
@else
<div id="login-error" class="mb-6 text-red-400 text-sm hidden"></div>
@endif


<form method="POST" action="/login" class="space-y-6">

@csrf

<div class="space-y-1">

<label class="text-[9px] uppercase font-bold text-slate-500 tracking-[0.2em] ml-1">
Email
</label>

<input
name="email"
type="email"
required
class="w-full h-12 px-4 rounded-md minimal-input text-slate-200 text-sm placeholder:text-slate-700"
placeholder="nombre@ejemplo.com"
/>

</div>


<div class="space-y-1">

<label class="text-[9px] uppercase font-bold text-slate-500 tracking-[0.2em] ml-1">
Contraseña
</label>

<div class="relative">

<input
name="password"
type="password"
required
class="w-full h-12 px-4 rounded-md minimal-input text-slate-200 text-sm placeholder:text-slate-700"
placeholder="••••••••"
/>

</div>

</div>


<div class="pt-4">

<button
type="submit"
class="w-full py-4 bg-white/[0.04] hover:bg-white/[0.08] text-white text-[11px] font-bold uppercase tracking-[0.2em] rounded border border-white/[0.1] transition-all active:scale-[0.98]"
>

Entrar al Sistema

</button>

</div>

</form>


<div class="mt-10 pt-8 border-t border-white/[0.04] text-center">
<p class="text-[10px] text-slate-600 uppercase tracking-widest">
Gestión de Cursos © 2024
</p>
</div>

</div>

</body>
</html>