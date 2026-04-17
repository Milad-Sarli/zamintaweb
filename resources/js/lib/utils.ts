import { page } from '@inertiajs/svelte';
import { clsx, type ClassValue } from 'clsx';
import { get } from 'svelte/store';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

/**
 * Translation helper for Svelte components.
 */
export function t(key: string, replacements: Record<string, string> = {}): string {
    const translations = get(page).props.translations as any;
    let translation = key.split('.').reduce((obj, i) => (obj ? obj[i] : null), translations) || key;

    Object.keys(replacements).forEach((r) => {
        translation = translation.replace(`:${r}`, replacements[r]);
    });

    return translation;
}

export type WithoutChild<T> = T extends { child?: any } ? Omit<T, 'child'> : T;
export type WithoutChildren<T> = T extends { children?: any } ? Omit<T, 'children'> : T;
export type WithoutChildrenOrChild<T> = WithoutChildren<WithoutChild<T>>;
export type WithElementRef<T, U extends HTMLElement = HTMLElement> = T & { ref?: U | null };

/**
 * Reveal action for scroll animations
 */
export function reveal(node: HTMLElement, options: { delay?: number, duration?: number, y?: number, x?: number, scale?: number, once?: boolean } = {}) {
    const {
        delay = 0,
        duration = 800,
        y = 30,
        x = 0,
        scale = 1,
        once = true
    } = options;

    node.style.opacity = '0';
    node.style.transform = `translate3d(${x}px, ${y}px, 0) scale(${scale})`;
    node.style.transition = `opacity ${duration}ms cubic-bezier(0.2, 0.8, 0.2, 1) ${delay}ms, transform ${duration}ms cubic-bezier(0.2, 0.8, 0.2, 1) ${delay}ms`;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                node.style.opacity = '1';
                node.style.transform = 'translate3d(0, 0, 0) scale(1)';
                if (once) observer.unobserve(node);
            } else if (!once) {
                node.style.opacity = '0';
                node.style.transform = `translate3d(${x}px, ${y}px, 0) scale(${scale})`;
            }
        });
    }, { threshold: 0.1 });

    observer.observe(node);

    return {
        destroy() {
            observer.unobserve(node);
        }
    };
}

export function toPersianDigits(n: number | string) {
    const persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    return n.toString().replace(/\d/g, x => persian[parseInt(x)]);
}

export function formatPrice(price: number) {
    return toPersianDigits(price.toLocaleString('en-US'));
}
