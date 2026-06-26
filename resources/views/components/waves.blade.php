{{-- ── Waves: nyambung dari background mesh ke footer ── --}}
<div class="footer-waves" aria-hidden="true">
    {{-- Kapal di tengah waves --}}
    <div class="ship-container">
        <canvas id="ship-canvas" width="200" height="200"></canvas>
    </div>

    {{-- Wave 3: belakang --}}
    <svg class="wsvg w3" width="5760" viewBox="0 0 5760 200" preserveAspectRatio="none"
        xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="lg3" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#c2c6cc" />
                <stop offset="100%" stop-color="#e8ecef" />
            </linearGradient>
        </defs>
        <path fill="url(#lg3)" opacity="0.85" d="
        M0,90 C90,30 180,150 360,90 C540,30 630,150 720,90
               C810,30 900,150 1080,90 C1260,30 1350,150 1440,90
               C1530,30 1620,150 1800,90 C1980,30 2070,150 2160,90
               C2250,30 2340,150 2520,90 C2700,30 2790,150 2880,90
        C2970,30 3060,150 3240,90 C3420,30 3510,150 3600,90
               C3690,30 3780,150 3960,90 C4140,30 4230,150 4320,90
               C4410,30 4500,150 4680,90 C4860,30 4950,150 5040,90
               C5130,30 5220,150 5400,90 C5580,30 5670,150 5760,90
        L5760,200 L0,200 Z" />
    </svg>

    {{-- Wave 2: tengah --}}
    <svg class="wsvg w2" width="5760" viewBox="0 0 5760 200" preserveAspectRatio="none"
        xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="lg2" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#d1d5db" />
                <stop offset="100%" stop-color="#e8ecef" />
            </linearGradient>
        </defs>
        <path fill="url(#lg2)" opacity="0.9" d="
        M0,110 C120,45 240,165 480,105 C720,45 840,165 960,105
               C1080,45 1200,165 1440,105
               C1560,45 1680,165 1920,105 C2160,45 2280,165 2400,105
               C2520,45 2640,165 2880,105
        C3000,45 3120,165 3360,105 C3600,45 3720,165 3840,105
               C3960,45 4080,165 4320,105
               C4440,45 4560,165 4800,105 C5040,45 5160,165 5280,105
               C5400,45 5520,165 5760,105
        L5760,200 L0,200 Z" />
    </svg>

    {{-- Wave 1: depan --}}
    <svg class="wsvg w1" width="5760" viewBox="0 0 5760 200" preserveAspectRatio="none"
        xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="lg1" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#e5e7eb" />
                <stop offset="100%" stop-color="#f3f4f6" />
            </linearGradient>
        </defs>
        <path fill="url(#lg1)" d="
        M0,125 C160,65 320,170 480,125 C640,80 800,170 960,125
               C1120,80 1280,170 1440,125
               C1600,65 1760,170 1920,125 C2080,80 2240,170 2400,125
               C2560,80 2720,170 2880,125
        C3040,65 3200,170 3360,125 C3520,80 3680,170 3840,125
               C4000,80 4160,170 4320,125
               C4480,65 4640,170 4800,125 C4960,80 5120,170 5280,125
               C5440,80 5600,170 5760,125
        L5760,200 L0,200 Z" />
    </svg>

</div>

<style>
    /* ── Waves ── */
    .footer-waves {
        position: relative;
        width: 100%;
        height: 160px;
        overflow: hidden;
        background: transparent;
        margin-top: -2px;
        pointer-events: none;
        padding-top: 17rem;
    }

    .ship-container {
        position: absolute;
        top: 25%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 5;
        pointer-events: none;
        animation: ship-float 3s ease-in-out infinite;
    }

    @keyframes ship-float {
        0%, 100% {
            transform: translate(-50%, -50%) translateY(-8px) rotate(-2deg);
        }
        25% {
            transform: translate(-50%, -50%) translateY(4px) rotate(1deg);
        }
        50% {
            transform: translate(-50%, -50%) translateY(8px) rotate(2deg);
        }
        75% {
            transform: translate(-50%, -50%) translateY(4px) rotate(1deg);
        }
    }

    #ship-canvas {
        display: block;
    }

    .wsvg {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 100%;
        display: block;
        will-change: transform;
    }

    .w3 {
        animation: wave-scroll 20s linear infinite;
        z-index: 8;
    }

    .w2 {
        animation: wave-scroll 28s linear infinite;
        z-index: 6;
    }

    .w1 {
        animation: wave-scroll 15s linear infinite;
        z-index: 4;
    }

    @keyframes wave-scroll {
        from {
            transform: translateX(0);
        }

        to {
            transform: translateX(-50%);
        }
    }
</style>

<script src="https://unpkg.com/zdog@1/dist/zdog.dist.min.js"></script>
<script>
(function() {
    const { Anchor, Shape, Ellipse, RoundedRect, easeInOut } = Zdog;

    const stroke = 0.2;
    const offsets = [1, 2, 3];

    const puffsOffset = -12;
    const puffsSpeed = 0.05;
    const puffsOffsets = Array(3)
        .fill()
        .map((_, i, { length }) => (puffsOffset / length) * i);

    const puffsOffsetX = (offset) =>
        easeInOut(1 - (puffsOffset - offset) / puffsOffset) * 8;
    const puffsStroke = (offset) => ((puffsOffset - offset) / puffsOffset) * 4;

    const colors = {
        hull: "hsl(0 0% 85%)",
        deck: "hsl(210 8% 75%)",
        cabin: "hsl(0 0% 90%)",
        chinmey: "hsl(0 0% 88%)",
        exhaust: "hsl(210 8% 70%)",
        windows: "hsl(210 10% 50%)",
        puffs: "hsl(0 0% 95%)"
    };

    const root = new Anchor();

    const hull = new Shape({
        addTo: root,
        color: colors.hull,
        stroke,
        fill: true,
        path: [
            { x: 13.5, y: 2.5 },
            {
                arc: [
                    { x: 13.5, y: 10 },
                    { x: 6, y: 10 }
                ]
            },
            { x: -6, y: 10 },
            {
                arc: [
                    { x: -13.5, y: 10 },
                    { x: -13.5, y: 2.5 }
                ]
            }
        ]
    });

    const deck = new Shape({
        addTo: root,
        color: colors.deck,
        stroke,
        fill: true,
        path: [
            { x: 3, y: 0 },
            { x: 11, y: 0 },
            {
                arc: [
                    { x: 13.5, y: 0 },
                    { x: 13.5, y: 2.5 }
                ]
            },
            { x: -13.5, y: 2.5 },
            { x: -13.5, y: -2.5 },
            {
                arc: [
                    { x: -13.5, y: -5 },
                    { x: -11, y: -5 }
                ]
            },
            { x: -8, y: -5 },
            {
                bezier: [
                    { x: -2, y: -5 },
                    { x: -2, y: 0 },
                    { x: 3, y: 0 }
                ]
            }
        ]
    });

    const obloo = new Ellipse({
        diameter: 2,
        color: colors.windows,
        stroke: 1,
        fill: true
    });

    for (const x of [-10, -5]) {
        obloo.copy({
            addTo: root,
            translate: {
                x,
                y: -1,
                z: offsets[2]
            }
        });
    }

    const cabin = new Shape({
        addTo: root,
        color: colors.cabin,
        stroke,
        fill: true,
        path: [
            { x: -8, y: -5 },
            { x: -8, y: -9 },
            {
                arc: [
                    { x: -8, y: -11.5 },
                    { x: -5.5, y: -11.5 }
                ]
            },
            { x: 1.5, y: -11.5 },
            {
                arc: [
                    { x: 3, y: -11.5 },
                    { x: 3, y: -9 }
                ]
            },
            { x: 3, y: 0 },
            {
                bezier: [
                    { x: -2, y: 0 },
                    { x: -2, y: -5 },
                    { x: -8, y: -5 }
                ]
            }
        ]
    });

    const window = new RoundedRect({
        color: colors.windows,
        stroke: 1,
        fill: true,
        width: 2.5,
        height: 2.5,
        cornerRadius: 0.5
    });

    for (const [x, y] of [
        [-4.5, -8],
        [-0.5, -8]
    ]) {
        window.copy({
            addTo: root,
            translate: {
                x,
                y,
                z: offsets[0]
            }
        });
    }

    const chimney = new Shape({
        addTo: root,
        color: colors.chinmey,
        stroke,
        fill: true,
        path: [
            { x: 4.5, y: 0 },
            { x: 4.5, y: -8 },
            { x: 9.5, y: -8 },
            { x: 11, y: 0 }
        ]
    });

    const exhaust = new Shape({
        addTo: root,
        color: colors.exhaust,
        stroke,
        fill: true,
        path: [
            { x: 4.5, y: -8 },
            { x: 4.5, y: -9.5 },
            { x: 9.5, y: -9.5 },
            { x: 9.5, y: -8 }
        ]
    });

    const puffs = new Anchor({
        addTo: root,
        translate: {
            x: 7,
            y: -12
        }
    });

    for (const offset of puffsOffsets) {
        const x = puffsOffsetX(offset);
        new Shape({
            addTo: puffs,
            stroke: puffsStroke(offset),
            color: colors.puffs,
            translate: {
                x,
                y: offset
            }
        });
    }

    const element = document.querySelector("#ship-canvas");
    if (!element) return;
    
    const { width, height } = element;
    const context = element.getContext("2d");
    const zoom = 6;

    context.lineCap = "round";
    context.lineJoin = "round";

    const render = () => {
        context.clearRect(0, 0, width, height);
        context.save();
        context.translate(width / 2, height / 2);
        context.scale(zoom, zoom);
        root.renderGraphCanvas(context);
        context.restore();
    };

    root.translate.y = 4;

    root.updateGraph();
    render();

    let frame = null;

    const animate = () => {
        for (const puff of puffs.children) {
            puff.translate.y -= puffsSpeed;
            if (puff.translate.y <= puffsOffset) {
                puff.translate.y = 0;
            }
            puff.stroke = puffsStroke(puff.translate.y);
            puff.translate.x = puffsOffsetX(puff.translate.y);
        }

        root.updateGraph();
        render();

        frame = requestAnimationFrame(animate);
    };

    const listener = (e) => {
        if (e.matches) {
            cancelAnimationFrame(frame);
        } else {
            frame = requestAnimationFrame(animate);
        }
    };

    const reducedMotion = matchMedia("(prefers-reduced-motion: reduce)");

    if (!reducedMotion.matches) {
        frame = requestAnimationFrame(animate);
    }

    reducedMotion.addEventListener("change", listener);
})();
</script>