#!/usr/bin/env node
/**
 * NicerApp WebOS - Headless Screenshot Engine (Puppeteer)
 * Handles broken HTTPS / Safe-Browsing blocked domains by forcing HTTP when needed.
 */

const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');

const targetUrl = process.argv[2];
const outputPath = process.argv[3];

if (!targetUrl || !outputPath) {
    console.error("Usage: node nodeScreenshot.js <url> <outputPath>");
    process.exit(1);
}

const targetDir = path.dirname(outputPath);
if (!fs.existsSync(targetDir)) {
    fs.mkdirSync(targetDir, { recursive: true });
}

const ENGINE_TIMEOUT_MS = 120 * 1000;
const globalTimeout = setTimeout(() => {
    console.error("CRITICAL FAILURE: Node execution exceeded safety threshold.");
    process.exit(1);
}, ENGINE_TIMEOUT_MS);
globalTimeout.unref();

process.on('uncaughtException', (err) => {
    console.error('UNCAUGHT EXCEPTION:', err.message);
    process.exit(1);
});
process.on('unhandledRejection', (reason) => {
    console.error('UNHANDLED REJECTION:', reason);
    process.exit(1);
});

(async () => {
    let browser = null;
    try {
        browser = await puppeteer.launch({
            args: [
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-dev-shm-usage',
                '--disable-web-security',
                '--disable-features=SafeBrowsingEnhancedProtection,IsolateOrigins,site-per-process',
                '--disable-client-side-phishing-detection',
                '--safebrowsing-disable-download-protection',
                '--safebrowsing-disable-extension-blacklist',
                '--disable-extensions',
                '--no-first-run',

                // WebGL / GPU fixes
                '--enable-gpu',                    // important – do NOT use --disable-gpu
                '--ignore-gpu-blocklist',
                '--use-gl=angle',
                '--use-angle=swiftshader-webgl',   // or just 'swiftshader'
                '--enable-unsafe-swiftshader',     // required on newer Chromium
                // alternative that sometimes works better:
                // '--use-gl=swiftshader',
            ],
            ignoreHTTPSErrors: true,
            headless: 'new'
        });

        const page = await browser.newPage();
        await page.setViewport({ width: 3840, height: 2160, deviceScaleFactor: 1 });
        await page.setUserAgent(
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
        );

        // Force HTTPS → HTTP for this domain (and www) so Safe Browsing can't kill us
        await page.setRequestInterception(true);
        page.on('request', (req) => {
            let url = req.url();
            if (
                url.startsWith('https://freeintertv.com') ||
                url.startsWith('https://www.freeintertv.com')
            ) {
                url = url.replace('https://', 'http://');
                req.continue({ url });
            } else {
                req.continue();
            }
        });

        let finalUrl = targetUrl;
        console.log(`STATUS: Navigating to ${finalUrl}`);

        try {
            await page.goto(finalUrl, {
                waitUntil: 'load',
                timeout: 120000
            });
            await new Promise(r => setTimeout(r, 10*1000)); // extra settle time
        } catch (err) {
            // HTTPS connection refused → try HTTP once
            if (
                (err.message.includes('ERR_CONNECTION_REFUSED') ||
                 err.message.includes('ERR_BLOCKED_BY_CLIENT')) &&
                finalUrl.startsWith('https://')
            ) {
                finalUrl = finalUrl.replace('https://', 'http://');
                console.log(`STATUS: HTTPS failed – retrying with HTTP → ${finalUrl}`);
                await page.goto(finalUrl, {
                    waitUntil: 'networkidle2',
                    timeout: 120000
                });
            } else {
                throw err;
            }
        }

        // Let dynamic content settle a bit
        await new Promise(r => setTimeout(r, 1500));

        await page.screenshot({
            path: outputPath,
            type: 'png'
        });

        console.log(`SUCCESS: Image written → ${outputPath}`);
        clearTimeout(globalTimeout);
        process.exit(0);

    } catch (error) {
        console.error('ENGINE ERROR:', error.message);
        process.exit(1);
    } finally {
        if (browser) {
            try { await browser.close(); } catch (_) {}
        }
    }
})();
