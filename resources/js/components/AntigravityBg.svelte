<script lang="ts">
    import { onMount } from 'svelte';
    import * as THREE from 'three';

    interface Props {
        count?: number;
        magnetRadius?: number;
        ringRadius?: number;
        waveSpeed?: number;
        waveAmplitude?: number;
        particleSize?: number;
        lerpSpeed?: number;
        color?: string;
        autoAnimate?: boolean;
        particleVariance?: number;
        rotationSpeed?: number;
        depthFactor?: number;
        pulseSpeed?: number;
        particleShape?: 'capsule' | 'sphere' | 'box' | 'tetrahedron';
        fieldStrength?: number;
        class?: string;
        style?: string;
    }

    let {
        count = 300,
        magnetRadius = 10,
        ringRadius = 10,
        waveSpeed = 0.4,
        waveAmplitude = 1,
        particleSize = 2,
        lerpSpeed = 0.1,
        color = '#38bdf8',
        autoAnimate = false,
        particleVariance = 1,
        rotationSpeed = 0,
        depthFactor = 1,
        pulseSpeed = 3,
        particleShape = 'capsule',
        fieldStrength = 10,
        class: className = '',
        style = '',
    }: Props = $props();

    let canvas: HTMLCanvasElement;

    interface ParticleData {
        t: number;
        speed: number;
        mx: number;
        my: number;
        mz: number;
        cx: number;
        cy: number;
        cz: number;
        randomRadiusOffset: number;
    }

    onMount(() => {
        const renderer = new THREE.WebGLRenderer({ canvas, alpha: true, antialias: true });
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(35, 1, 0.1, 1000);
        camera.position.set(0, 0, 50);

        const geoMap: Record<string, THREE.BufferGeometry> = {
            capsule: new THREE.CapsuleGeometry(0.035, 0.14, 4, 8),
            sphere: new THREE.SphereGeometry(0.12, 8, 8),
            box: new THREE.BoxGeometry(0.15, 0.15, 0.15),
            tetrahedron: new THREE.TetrahedronGeometry(0.15),
        };
        const geometry = geoMap[particleShape];
        const material = new THREE.MeshBasicMaterial({ color: '#ffffff', transparent: true, opacity: 1 });
        const mesh = new THREE.InstancedMesh(geometry, material, count);
        mesh.frustumCulled = false;
        scene.add(mesh);

        // Pre-allocate instanceColor array to avoid WebGL errors
        const initialColor = new THREE.Color('#ffffff');
        for (let i = 0; i < count; i++) {
            mesh.setColorAt(i, initialColor);
        }

        const dummy = new THREE.Object3D();
        const tmpVec = new THREE.Vector3();
        const particles: ParticleData[] = [];

        const colorPalette = [
            { angle: -Math.PI, color: new THREE.Color('#4285F4') }, // Blue
            { angle: -Math.PI * 0.66, color: new THREE.Color('#34A853') }, // Green
            { angle: -Math.PI * 0.33, color: new THREE.Color('#FBBC05') }, // Yellow
            { angle: 0, color: new THREE.Color('#EA4335') }, // Red
            { angle: Math.PI * 0.33, color: new THREE.Color('#D946EF') }, // Pink/Magenta
            { angle: Math.PI * 0.66, color: new THREE.Color('#6366F1') }, // Indigo/Purple
            { angle: Math.PI, color: new THREE.Color('#4285F4') }, // Blue
        ];

        function getColorForAngle(ang: number): THREE.Color {
            let i = 0;
            for (; i < colorPalette.length - 1; i++) {
                if (ang >= colorPalette[i].angle && ang <= colorPalette[i + 1].angle) {
                    break;
                }
            }
            const p1 = colorPalette[i];
            const p2 = colorPalette[i + 1];
            const ratio = (ang - p1.angle) / (p2.angle - p1.angle);
            return p1.color.clone().lerp(p2.color, ratio);
        }

        function getViewportSize() {
            const h = camera.position.z * 2 * Math.tan((camera.fov * Math.PI) / 360);
            const w = h * camera.aspect;
            return { w, h };
        }

        function initParticles() {
            const { w, h } = getViewportSize();
            for (let i = 0; i < count; i++) {
                const p: ParticleData = {
                    t: Math.random() * 100,
                    speed: 0.01 + Math.random() / 200,
                    mx: (Math.random() - 0.5) * w * 2,
                    my: (Math.random() - 0.5) * h * 2,
                    mz: (Math.random() - 0.5) * 8,
                    cx: 0, cy: 0, cz: 0,
                    randomRadiusOffset: (Math.random() - 0.5) * 2,
                };
                p.cx = p.mx;
                p.cy = p.my;
                p.cz = p.mz;
                particles.push(p);
            }
        }
        initParticles();

        const mouse = { x: 0, y: 0 };
        const virtualMouse = { x: 0, y: 0 };
        let lastMouseTime = 0;

        const handleMouse = (ce: { clientX: number; clientY: number }) => {
            const rect = canvas.getBoundingClientRect();
            mouse.x = ((ce.clientX - rect.left) / rect.width) * 2 - 1;
            mouse.y = -((ce.clientY - rect.top) / rect.height) * 2 + 1;
            lastMouseTime = performance.now();
        };

        window.addEventListener('mousemove', (e) => handleMouse(e));
        window.addEventListener('touchmove', (e) => {
            if (e.touches.length > 0) handleMouse(e.touches[0]);
        });

        const resize = () => {
            const parent = canvas.parentElement;
            if (!parent) return;
            const w = parent.clientWidth;
            const h = parent.clientHeight;
            renderer.setSize(w, h);
            camera.aspect = w / h;
            camera.updateProjectionMatrix();
        };
        resize();
        const ro = new ResizeObserver(resize);
        ro.observe(canvas.parentElement!);

        const clock = new THREE.Clock();

        function animate() {
            const t = clock.getElapsedTime();
            const { w: vw, h: vh } = getViewportSize();

            let destX = mouse.x * vw * 0.5;
            let destY = mouse.y * vh * 0.5;

            if (autoAnimate && performance.now() - lastMouseTime > 2000) {
                destX = Math.sin(t * 0.5) * vw * 0.25;
                destY = Math.cos(t * 0.5 * 2) * vh * 0.25;
            }

            virtualMouse.x += (destX - virtualMouse.x) * 0.05;
            virtualMouse.y += (destY - virtualMouse.y) * 0.05;

            const targetX = virtualMouse.x;
            const targetY = virtualMouse.y;

            for (let i = 0; i < count; i++) {
                const p = particles[i];
                p.t += p.speed / 2;

                const projectionFactor = 1 - p.cz / 50;
                const ptx = targetX * projectionFactor;
                const pty = targetY * projectionFactor;

                const dx = p.mx - ptx;
                const dy = p.my - pty;
                const dist = Math.sqrt(dx * dx + dy * dy);

                let tx = p.mx;
                let ty = p.my;
                let tz = p.mz * depthFactor;

                if (dist < magnetRadius) {
                    const angle = Math.atan2(dy, dx) + t * rotationSpeed;
                    const wave1 = Math.sin(p.t * waveSpeed + angle) * (0.5 * waveAmplitude);
                    const wave2 = Math.sin(p.t * waveSpeed * 0.7 + angle * 2) * (0.25 * waveAmplitude);
                    const wave3 = Math.sin(p.t * waveSpeed * 0.3 - angle * 1.5) * (0.15 * waveAmplitude);
                    const wave = wave1 + wave2 + wave3;
                    const deviation = p.randomRadiusOffset * (5 / (fieldStrength + 0.1));
                    const rr = ringRadius + wave + deviation;
                    tx = ptx + rr * Math.cos(angle);
                    ty = pty + rr * Math.sin(angle);
                    tz = p.mz * depthFactor + Math.sin(p.t) * (1 * waveAmplitude * depthFactor) + Math.sin(p.t * 0.5 + angle) * (0.5 * waveAmplitude * depthFactor);
                }

                p.cx += (tx - p.cx) * lerpSpeed;
                p.cy += (ty - p.cy) * lerpSpeed;
                p.cz += (tz - p.cz) * lerpSpeed;

                dummy.position.set(p.cx, p.cy, p.cz);
                tmpVec.set(ptx, pty, p.cz);
                dummy.lookAt(tmpVec);
                dummy.rotateX(Math.PI / 2);

                const cdist = Math.sqrt((p.cx - ptx) ** 2 + (p.cy - pty) ** 2);
                const dr = Math.abs(cdist - ringRadius);
                let sf = 1 - dr / 10;
                sf = Math.max(0, Math.min(1, sf));
                const finalScale = sf * (0.8 + Math.sin(p.t * pulseSpeed) * 0.2 * particleVariance) * particleSize;
                dummy.scale.set(finalScale, finalScale, finalScale);
                dummy.updateMatrix();
                mesh.setMatrixAt(i, dummy.matrix);

                // Set dynamic color based on angle
                const currentAngle = Math.atan2(p.cy - pty, p.cx - ptx);
                const currentCol = getColorForAngle(currentAngle);
                mesh.setColorAt(i, currentCol);
            }

            mesh.instanceMatrix.needsUpdate = true;
            if (mesh.instanceColor) {
                mesh.instanceColor.needsUpdate = true;
            }
            renderer.render(scene, camera);
            requestAnimationFrame(animate);
        }

        const animId = requestAnimationFrame(animate);

        return () => {
            cancelAnimationFrame(animId);
            ro.disconnect();
            renderer.dispose();
            geometry.dispose();
            material.dispose();
        };
    });
</script>

<div class={className} style="position: relative; {style}">
    <canvas bind:this={canvas} style="display: block; width: 100%; height: 100%; pointer-events: none;"></canvas>
</div>
