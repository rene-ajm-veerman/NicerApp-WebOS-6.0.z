// beat-processor.js  ← this file is loaded via addModule()
class BeatPulseProcessor extends AudioWorkletProcessor {
  constructor() {
    super();
    this.energyHistory = new Float32Array(43); // example: rolling window for beat detection
    this.energyIndex = 0;
    this.threshold = 0.15; // tune this
    // You can use port for communication with main thread
    this.port.onmessage = (e) => {
      if (e.data.threshold !== undefined) this.threshold = e.data.threshold;
      // etc. for dynamic params from UI
    };
  }

  process(inputs, outputs, parameters) {
    const input = inputs[0]; // first channel (mono or left)
    if (!input || input.length === 0) return true;

    // Example simple energy-based beat detection
    let energy = 0;
    for (let i = 0; i < input.length; i++) {
      const sample = input[i];
      energy += sample * sample;
    }
    energy /= input.length;

    // Rolling average / variance check (better than fixed threshold)
    const avgEnergy = this.energyHistory.reduce((a, b) => a + b, 0) / this.energyHistory.length;
    if (energy > avgEnergy * 1.6 && energy > this.threshold) {  // adjust multipliers
      // Send beat event to main thread
      this.port.postMessage({ type: 'beat', energy });
    }

    // Update history
    this.energyHistory[this.energyIndex] = energy;
    this.energyIndex = (this.energyIndex + 1) % this.energyHistory.length;

    // Copy input → output (passthrough)
    for (let ch = 0; ch < outputs[0].length; ch++) {
      const outputCh = outputs[0][ch];
      const inputCh = inputs[0][ch] || inputs[0][0]; // fallback to mono
      for (let i = 0; i < input.length; i++) {
        outputCh[i] = inputCh[i];
      }
    }

    return true; // keep the processor alive
  }
}

registerProcessor('beat-pulse-processor', BeatPulseProcessor);
