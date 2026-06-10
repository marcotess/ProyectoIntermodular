@extends('layouts.guest')

@section('title', 'Acceso | Gestion de Cursos')

@section('content')
<!-- en login separo bien la parte de ambiente visual de la parte practica del formulario. -->
<main class="relative flex min-h-screen items-center justify-center overflow-hidden px-5 py-10">
	<div class="absolute inset-0">
		<div class="absolute left-[10%] top-[12%] h-40 w-40 rounded-full bg-[rgba(124,45,60,0.14)] blur-3xl"></div>
		<div class="absolute bottom-[10%] right-[8%] h-56 w-56 rounded-full bg-[rgba(124,45,60,0.10)] blur-3xl"></div>
	</div>

	<div class="relative z-10 w-full max-w-[1120px]">
		<div class="mb-8 text-center">
			<a href="{{ route('login') }}" class="brand-mark inline-block text-[22px] font-semibold uppercase text-[color:var(--accent-strong)] sm:text-[28px]">Gestion de Cursos</a>
			<p class="mt-4 text-[12px] font-semibold uppercase tracking-[0.24em] text-[color:var(--muted)]">Acceso a la plataforma academica</p>
		</div>

		<div class="guest-surface grid overflow-hidden rounded-[32px] lg:grid-cols-[1.1fr_0.9fr]">
			<section class="hidden border-r border-[color:var(--line)] px-8 py-10 lg:flex lg:flex-col lg:justify-between xl:px-12">
				<div>
					<div class="inline-flex rounded-full border border-[color:var(--line)] bg-white/70 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--accent-strong)]">Antesala digital</div>
					<h1 class="mt-8 max-w-[15ch] text-4xl font-extrabold leading-tight text-[color:var(--ink)] xl:text-5xl">Donde cada proyecto empieza con una pantalla en calma.</h1>
					<p class="mt-6 max-w-[52ch] text-[15px] leading-8 text-[color:var(--muted)]">Menos ruido, más foco. Este acceso prepara el tono del trabajo: limpio, ordenado y con una presencia visual más cuidada para entrar directamente en contexto.</p>
				</div>

				<div class="grid gap-4 sm:grid-cols-3">
					<article class="rounded-[24px] border border-[color:var(--line)] bg-white/70 p-5">
						<p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Ritmo</p>
						<p class="mt-3 text-[13px] leading-6 text-[color:var(--ink)]">Una entrada serena que evita saturar antes de empezar.</p>
					</article>
					<article class="rounded-[24px] border border-[color:var(--line)] bg-white/70 p-5">
						<p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Presencia</p>
						<p class="mt-3 text-[13px] leading-6 text-[color:var(--ink)]">Tipografía, aire y contraste pensados para entrar con claridad.</p>
					</article>
					<article class="rounded-[24px] border border-[color:var(--line)] bg-white/70 p-5">
						<p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Entrada</p>
						<p class="mt-3 text-[13px] leading-6 text-[color:var(--ink)]">Una primera impresión más humana y menos mecánica.</p>
					</article>
				</div>
			</section>

			<section class="px-6 py-8 sm:px-10 sm:py-10 xl:px-12">
				<div class="mx-auto max-w-[420px]">
					<p class="text-[11px] font-semibold uppercase tracking-[0.20em] text-[color:var(--accent-strong)]">Iniciar sesion</p>
					<h2 class="mt-3 text-3xl font-extrabold text-[color:var(--ink)]">Accede a tu espacio de trabajo</h2>
					<p class="mt-3 text-[14px] leading-7 text-[color:var(--muted)]">Usa tus credenciales para consultar cursos, proyectos, documentos y notificaciones desde una interfaz mas cuidada.</p>

					@if ($errors->any())
						<div id="login-error" class="mt-6 rounded-2xl border border-[rgba(160,63,81,0.22)] bg-[rgba(160,63,81,0.08)] px-4 py-3 text-[13px] text-[color:var(--danger)]">{{ $errors->first() }}</div>
					@else
						<div id="login-error" class="mt-6 hidden rounded-2xl border border-[rgba(160,63,81,0.22)] bg-[rgba(160,63,81,0.08)] px-4 py-3 text-[13px] text-[color:var(--danger)]"></div>
					@endif

					<form method="POST" action="{{ route('login.submit') }}" data-api-login-form class="mt-8 space-y-5">
						@csrf

						<div>
							<label for="email" class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Correo electronico</label>
							<input id="email" name="email" type="email" required class="field-shell h-14 w-full rounded-2xl px-4 text-[15px] text-[color:var(--ink)] placeholder:text-[color:var(--muted)]" placeholder="nombre@ejemplo.com" />
						</div>

						<div>
							<label for="password" class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Contrasena</label>
							<input id="password" name="password" type="password" required class="field-shell h-14 w-full rounded-2xl px-4 text-[15px] text-[color:var(--ink)] placeholder:text-[color:var(--muted)]" placeholder="Introduce tu contrasena" />
						</div>

						<button type="submit" class="mt-2 inline-flex w-full items-center justify-center rounded-2xl bg-[color:var(--accent)] px-6 py-4 text-[12px] font-bold uppercase tracking-[0.20em] text-white shadow-[0_18px_36px_rgba(124,45,60,0.22)] transition hover:bg-[color:var(--accent-strong)] active:scale-[0.99]">Acceder a la aplicacion</button>
					</form>

				</div>
			</section>
		</div>
	</div>
</main>
@endsection