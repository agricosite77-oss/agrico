<?php

return [
    // Root folder for all soil scan training data (Real_Soil/, Fake_Soil/, the SQLite
    // index, and the Munsell centroid cache). Defaults to python/Scan_Soil_Image
    // relative to the project root — override via .env only if you need it elsewhere.
    'scan_soil_root' => env('SCAN_SOIL_ROOT', base_path('python/Scan_Soil_Image')),

    // Stage 2 lighting-normalization mode passed to the Python pipeline.
    // Values: 'classical' | 'dinolight' | 'omnilight' | 'promptnorm' | 'flatten'
    //   classical  — LAB gamma / CLAHE (default; no sidecar required)
    //   dinolight  — DINOLight sidecar on port 8501 (falls back to classical)
    //   omnilight  — OmniLight sidecar on port 8501 (falls back to classical)
    //   promptnorm — PromptNorm sidecar on port 8500 (falls back to classical)
    //   flatten    — local Gaussian blur-and-divide (scipy, no sidecar)
    'stage2_mode' => env('STAGE2_MODE', 'classical'),
];
