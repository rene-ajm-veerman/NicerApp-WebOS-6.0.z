<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Gradient Stop Transition Demo</title>
<style>
/* Register the colors so the browser can interpolate them */
@property --from {
    syntax: "<color>";
    inherits: false;
    initial-value: rgba(255, 80, 100, 0.7);
}

@property --to {
    syntax: "<color>";
    inherits: false;
    initial-value: rgba(80, 120, 255, 0.85);
}

@property --mid {
    syntax: "<color>";
    inherits: false;
    initial-value: rgba(180, 80, 255, 0.6);
}

body {
    margin: 0;
    min-height: 100vh;
    display: grid;
    place-items: center;
    background: #111;
    font-family: system-ui, sans-serif;
}

.box {
    width: 320px;
    height: 200px;
    border-radius: 24px;
    display: grid;
    place-items: center;
    color: white;
    font-size: 1.4rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    cursor: pointer;
    user-select: none;

    /* The gradient uses the registered custom properties */
    background: linear-gradient(
        135deg,
        var(--from),
                                var(--mid) 50%,
                                var(--to)
    );

    /* Smooth transition of the stops */
    transition:
    --from 0.8s ease,
    --mid  0.8s ease,
    --to   0.8s ease,
    transform 0.3s ease,
    box-shadow 0.3s ease;

    box-shadow: 0 20px 40px rgba(0,0,0,0.4);
}

.box:hover {
    --from: rgba(50, 220, 140, 0.85);
    --mid:  rgba(255, 180, 50, 0.75);
    --to:   rgba(255, 80, 180, 0.9);

    transform: scale(1.04);
    box-shadow: 0 30px 60px rgba(0,0,0,0.5);
}

/* Optional continuous pulse version */
.box.pulse {
    animation: pulse 3s ease-in-out infinite alternate;
}

@keyframes pulse {
    to {
        --from: rgba(120, 60, 255, 0.9);
        --mid:  rgba(255, 100, 180, 0.7);
        --to:   rgba(50, 200, 255, 0.85);
    }
}
</style>
</head>
<body>

<div class="box">
Hover me
</div>

<!-- Uncomment the next line if you want continuous animation instead of hover -->
< <div class="box pulse">Pulsing</div>

</body>
</html>
