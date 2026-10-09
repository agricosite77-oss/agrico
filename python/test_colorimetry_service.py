import os
import sys
import unittest

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

from colorimetry_service import build_output, resolve_status


class FakeResult:
    """Stand-in for a ClassificationResult-like object."""
    def __init__(self, status=None):
        self.status = status
        self.label = "real_soil"
        self.confidence = 0.7
        self.rgb = (100, 90, 80)
        self.texture_score = 0.5


class NonDictRecord:
    """An object that is neither a dict nor has a .get(), to exercise the isinstance guard."""
    def __init__(self, status=None):
        self.status = status
        self.predicted_label = "real_soil"
        self.confidence = 0.7
        self.image_id = "test_img_123"
        self.file_path = "Scan_Soil_Image/Real_Soil/test.jpg"
        self.source = "upload"

    def __getitem__(self, key):
        return getattr(self, key)


def test_record_is_none_falls_back_to_unclassified():
    """record=None should never raise AttributeError; status should fall back to 'unclassified'."""
    result = FakeResult(status=None)
    record = {
        "predicted_label": "real_soil",
        "confidence": 0.7,
        "file_path": "x",
        "source": "upload",
        "image_id": "1",
        "status": None,
    }

    output = build_output(record=record, result=result, api_regions=[])
    assert output["status"] == "unclassified"


def test_record_is_non_dict_object_falls_back_safely():
    """A record that is an arbitrary object (no .get) should use getattr, not crash."""
    record = NonDictRecord(status=None)
    result = FakeResult(status=None)

    output = build_output(record=record, result=result, api_regions=[])
    assert output["status"] == "unclassified"


def test_record_status_missing_but_result_has_status():
    """If the DB record has no status but the in-memory result does, prefer the result's status."""
    record = {
        "predicted_label": "real_soil",
        "confidence": 0.7,
        "file_path": "x",
        "source": "upload",
        "image_id": "1",
        "status": None,
    }
    result = FakeResult(status="needs_review")

    output = build_output(record=record, result=result, api_regions=[])
    assert output["status"] == "needs_review"


class TestColorimetryServiceStatus(unittest.TestCase):
    def test_record_is_none_falls_back_to_unclassified(self):
        test_record_is_none_falls_back_to_unclassified()

    def test_record_is_non_dict_object_falls_back_safely(self):
        test_record_is_non_dict_object_falls_back_safely()

    def test_record_status_missing_but_result_has_status(self):
        test_record_status_missing_but_result_has_status()


if __name__ == "__main__":
    unittest.main()
