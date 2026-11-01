<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Per-Letter Gradient Text</title>
<style>
@property --from {
    syntax: "<color>";
    inherits: false;
    initial-value: rgba(255, 90, 120, 0.95);
}

@property --mid {
    syntax: "<color>";
    inherits: false;
    initial-value: rgba(140, 80, 255, 0.9);
}

@property --to {
    syntax: "<color>";
    inherits: false;
    initial-value: rgba(60, 180, 255, 0.95);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background: #0a0a0f;
    color: white;
    font-family: system-ui, -apple-system, sans-serif;
    line-height: 1.55;
    min-height: 100vh;
}

.container {
    max-width: 900px;
    margin: 0 auto;
    padding: 80px 40px 120px;
}

h1 {
    font-size: clamp(2.8rem, 6vw, 4.5rem);
    font-weight: 800;
    margin-bottom: 2rem;
    letter-spacing: -0.03em;
}

p {
    font-size: clamp(1.6rem, 3.2vw, 2.3rem);
    font-weight: 500;
    margin-bottom: 2.2rem;
    letter-spacing: -0.01em;
}

/* Each letter gets its own gradient */
.letter {
    display: inline-block;          /* needed for transform if you want */
    background: linear-gradient(
        120deg,
        var(--from),
                                var(--mid) 50%,
                                var(--to)
    );
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;

    /* Continuous color shift */
    animation: colorShift 7s ease-in-out infinite alternate;
}

@keyframes colorShift {
    0% {
        --from: rgba(255, 90, 120, 0.95);
        --mid:  rgba(140, 80, 255, 0.9);
        --to:   rgba(60, 180, 255, 0.95);
    }
    50% {
        --from: rgba(50, 220, 160, 0.95);
        --mid:  rgba(255, 160, 50, 0.9);
        --to:   rgba(255, 80, 200, 0.95);
    }
    100% {
        --from: rgba(100, 140, 255, 0.95);
        --mid:  rgba(255, 100, 180, 0.9);
        --to:   rgba(80, 255, 200, 0.95);
    }
}

/* Optional: staggered wave effect (uncomment the animation-delay line in JS) */
/* .letter { animation-delay: calc(var(--i) * 0.03s); } */
</style>
</head>
<body>

<div class="container">
<h1 class="gradient-text">Lorem Ipsum Gradient</h1>

<p class="gradient-text">
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
</p>

<p class="gradient-text">
Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
</p>

<p class="gradient-text">
Curabitur pretium tincidunt lacus. Nulla gravida orci a odio. Nullam varius, turpis et commodo pharetra, est eros bibendum elit, nec luctus magna felis sollicitudin mauris. Integer in mauris eu nibh euismod gravida.
</p>

<p class="gradient-text">
Duis ac tellus et risus vulputate vehicula. Donec lobortis risus a elit. Etiam tempor. Ut ullamcorper, ligula eu tempor congue, eros est euismod turpis, id tincidunt sapien risus a quam. Maecenas fermentum consequat mi.
</p>

<p class="gradient-text">
Donec fermentum. Pellentesque malesuada nulla a mi. Duis sapien sem, aliquet nec, feugiat eget, luctus sit amet, nunc. Ut lectus. Integer euismod lacus luctus magna. Quisque cursus, metus vitae pharetra auctor, sem massa mattis sem, at interdum magna augue eget diam.
</p>

<p class="gradient-text">
Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Morbi lacinia molestie dui. Praesent blandit dolor. Sed non quam. In vel mi sit amet augue congue elementum. Morbi in ipsum sit amet pede facilisis laoreet.
</p>

<p class="gradient-text">
Donec lacus nunc, viverra nec, blandit vel, egestas et, augue. Vestibulum tincidunt malesuada tellus. Ut ultrices ultrices enim. Curabitur sit amet mauris. Morbi in dui quis est pulvinar ullamcorper. Nulla facilisi.
</p>
</div>

<script>
// Automatically wrap every letter (and keep spaces)
document.querySelectorAll('.gradient-text').forEach(el => {
    const text = el.textContent;
    el.innerHTML = '';

[...text].forEach((char, i) => {
    const span = document.createElement('span');
    span.className = 'letter';
span.textContent = char === ' ' ? '\u00A0' : char; // non-breaking space
span.style.setProperty('--i', i);                 // for optional stagger
// span.style.animationDelay = `${i * 0.025}s`;  // ← uncomment for wave effect
el.appendChild(span);
});
});
</script>

</body>
</html>
