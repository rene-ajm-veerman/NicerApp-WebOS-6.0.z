<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Scrolling Gradient Text Demo</title>
<style>
/* Register the colors */
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
    line-height: 1.5;
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
    margin-bottom: 1.5rem;
    letter-spacing: -0.03em;
}

.gradient-text {
    /* The magic */
    background: linear-gradient(
        120deg,
        var(--from),
                                var(--mid) 45%,
                                var(--to)
    );
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;

    /* Smooth color transitions */
    transition:
    --from 1.1s ease,
    --mid  1.1s ease,
    --to   1.1s ease;
}

/* Continuous gentle animation */
.gradient-text {
    animation: colorShift 8s ease-in-out infinite alternate;
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

p {
    font-size: clamp(1.6rem, 3.2vw, 2.4rem);
    font-weight: 500;
    margin-bottom: 2rem;
    letter-spacing: -0.01em;
}

/* Optional: make the whole text block react on hover too */
.container:hover .gradient-text {
    animation-play-state: paused; /* pause continuous anim on hover if you want */
}
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

<p class="gradient-text">
Integer lacinia sollicitudin massa. Cras metus. Sed aliquet risus a tortor. Integer id quam. Morbi mi. Quisque nisl felis, venenatis tristique, dignissim in, ultrices sit amet, augue. Proin sodales libero eget ante.
</p>

<p class="gradient-text">
Nulla quam. Aenean laoreet. Vestibulum nisi lectus, commodo ac, facilisis ac, ultricies eu, pede. Ut orci risus, accumsan porttitor, cursus quis, aliquet eget, justo. Sed pretium blandit orci.
</p>

<p class="gradient-text">
Ut eu diam at pede suscipit sodales. Aenean lectus elit, fermentum non, convallis id, sagittis at, neque. Nullam mauris orci, aliquam et, fringilla sit amet, interdum id, nisi. Integer velit.
</p>

<p class="gradient-text">
Sed nisi. Nulla facilisi. Sed tortor. Integer euismod lacus luctus magna. Quisque cursus, metus vitae pharetra auctor, sem massa mattis sem, at interdum magna augue eget diam. Vestibulum ante ipsum primis.
</p>

<p class="gradient-text">
In faucibus orci luctus et ultrices posuere cubilia Curae; Morbi lacinia molestie dui. Praesent blandit dolor. Sed non quam. In vel mi sit amet augue congue elementum. Morbi in ipsum sit amet pede facilisis laoreet.
</p>
</div>

</body>
</html>
