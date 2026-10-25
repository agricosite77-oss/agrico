#!/usr/bin/env python3
"""
cache_builder.py - Convenience wrapper for build_stage1_reference_cache.py
"""
import sys
import os

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from build_stage1_reference_cache import main

if __name__ == "__main__":
    sys.exit(main())
