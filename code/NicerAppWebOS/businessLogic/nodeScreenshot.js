#!/usr/bin/env node

/**
 * NicerApp WebOS - Headless Screenshot Engine (Puppeteer Wrapper)
 * Designed for reliable background execution under Apache / www-data environments.
 */

const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');

// --- 1. SANE PARAMETER CHECKS ---
const targetUrl = process.argv[2];
const outputPath = process.argv[3];

if (!targetUrl || !outputPath) {
    console.error("CRITICAL STATUS: Missing arguments.");
    console.error("Usage: node screenshot_other.js <url> <outputPath>");
    process.exit(1);
}

// Ensure the directory bucket for the image output exists
const targetDir = path.dirname(outputPath);
if (!fs.existsSync(targetDir)) {
    fs.mkdirSync(targetDir, { recursive: true });
}

// --- 2. GLOBAL PROCESS FAIL-SAFES ---
// This guarantees PHP isn't left hanging on exec() forever if Chrome freezes
const ENGINE_TIMEOUT_MS = 120 * 1000; 
const globalTimeout = setTimeout(() => {
    console.error("CRITICAL FAILURE: Node execution exceeded safety threshold (45s). Force terminating.");
    process.exit(1);
}, ENGINE_TIMEOUT_MS);
globalTimeout.unref(); // Allows clean exit if execution completes earlier

process.on('uncaughtException', (err) => {
    console.error('UNCAUGHT EXCEPTION RUNTIME:', err.message);
    console.error(err.stack);
    process.exit(1);
});

process.on('unhandledRejection', (reason) => {
    console.error('UNHANDLED REJECTION RUNTIME:', reason);
    process.exit(1);
});

// --- 3. MAIN RUNTIME EXECUTION ---
(async () => {
    let browser = null;
    try {
        // Launch configurations tailored heavily for standard Linux servers running Apache
        const browser = await puppeteer.launch({
            // Forces Puppeteer to bypass root user security execution checks
            args: [
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-dev-shm-usage'
            ],
            // Explicitly bypasses path searching errors by letting Puppeteer
            // resolve its internal binary directly from its active installation folder
            ignoreDefaultArgs: ['--disable-extensions']
        });

        const page = await browser.newPage();
        
        // Define high-resolution canvas size matching your structural array logic
        await page.setViewport({
            width: 3840,
            height: 2160,
            deviceScaleFactor: 1
        });

        // Set an explicit user agent string so modern websites don't flag the worker as a headless bot
        await page.setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');

        console.log(`STATUS: Attempting connection to target domain -> ${targetUrl}`);

        // 'networkidle2' tells Puppeteer navigation is complete when there are no more than 2 network connections open.
        // This stops tracking pixels, ad engines, and sockets from causing infinite delays.
        await page.goto(targetUrl, {
            waitUntil: 'networkidle2',
            timeout: 120 * 1000 // 120-second target page response window
        });

        // Give dynamic UI rendering arrays or CSS transitions a brief window to settle down
        await new Promise(resolve => setTimeout(resolve, 1000));

        // Generate the file output
        await page.screenshot({
            path: outputPath,
            //fullPage: true, // Auto-expand heights to capture entire vertical documents
            type: 'png'
        });

        console.log(`SUCCESS: Image output safely pushed to disk -> ${outputPath}`);
        clearTimeout(globalTimeout);
        process.exit(0);

    } catch (error) {
        console.error("ENGINE ERROR: Failed to render layout screenshot.");
        console.error(error.message);
        process.exit(1);
    } finally {
        if (browser !== null) {
            await browser.close();
        }
    }
})();

