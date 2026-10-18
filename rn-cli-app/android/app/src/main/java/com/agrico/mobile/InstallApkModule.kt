package com.agrico.mobile

import android.content.Intent
import android.net.Uri
import android.os.Build
import androidx.core.content.FileProvider
import com.facebook.react.bridge.ReactApplicationContext
import com.facebook.react.bridge.ReactContextBaseJavaModule
import com.facebook.react.bridge.ReactMethod
import com.facebook.react.bridge.Promise
import java.io.File

class InstallApkModule(private val reactContext: ReactApplicationContext) : ReactContextBaseJavaModule(reactContext) {
    override fun getName(): String = "InstallApk"

    @ReactMethod
    fun install(filePath: String, promise: Promise) {
        try {
            val file = File(filePath)
            if (!file.exists()) {
                promise.reject("FILE_NOT_FOUND", "APK file not found at $filePath")
                return
            }
            val intent = Intent(Intent.ACTION_VIEW)
            intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
            intent.addFlags(Intent.FLAG_GRANT_READ_URI_PERMISSION)
            val uri: Uri = FileProvider.getUriForFile(
                reactContext,
                "${reactContext.packageName}.provider",
                file
            )
            intent.setDataAndType(uri, "application/vnd.android.package-archive")
            reactContext.startActivity(intent)
            promise.resolve(true)
        } catch (e: Exception) {
            promise.reject("INSTALL_ERROR", e.message, e)
        }
    }

    @ReactMethod
    fun extractSoilColor(filePath: String, promise: Promise) {
        try {
            val cleanPath = filePath.replace("file://", "")
            val file = File(cleanPath)
            if (!file.exists()) {
                promise.reject("FILE_NOT_FOUND", "File not found at $cleanPath")
                return
            }

            // Downsample image for high performance (~150x150 max)
            val bounds = android.graphics.BitmapFactory.Options().apply { inJustDecodeBounds = true }
            android.graphics.BitmapFactory.decodeFile(file.absolutePath, bounds)

            val maxDim = Math.max(bounds.outWidth, bounds.outHeight)
            var sampleSize = 1
            if (maxDim > 200) {
                sampleSize = maxDim / 150
            }

            val decodeOpts = android.graphics.BitmapFactory.Options().apply {
                inSampleSize = Math.max(1, sampleSize)
                inPreferredConfig = android.graphics.Bitmap.Config.ARGB_8888
            }

            val bitmap = android.graphics.BitmapFactory.decodeFile(file.absolutePath, decodeOpts)
            if (bitmap == null) {
                promise.reject("DECODE_ERROR", "Failed to decode image at $cleanPath")
                return
            }

            // Center 50% region where the soil sample reticle is focused
            val w = bitmap.width
            val h = bitmap.height
            val startX = (w * 0.25).toInt()
            val endX = (w * 0.75).toInt()
            val startY = (h * 0.25).toInt()
            val endY = (h * 0.75).toInt()

            var totalR = 0L
            var totalG = 0L
            var totalB = 0L
            var count = 0L
            var skinPixels = 0L
            var totalLuminance = 0.0
            var totalLuminanceSq = 0.0

            for (y in startY until endY) {
                for (x in startX until endX) {
                    val pixel = bitmap.getPixel(x, y)
                    val r = (pixel shr 16) and 0xFF
                    val g = (pixel shr 8) and 0xFF
                    val b = pixel and 0xFF
                    totalR += r
                    totalG += g
                    totalB += b
                    count++

                    val lum = 0.2989 * r + 0.5870 * g + 0.1140 * b
                    totalLuminance += lum
                    totalLuminanceSq += (lum * lum)

                    // YCbCr skin chrominance locus check
                    val cb = -0.168736 * r - 0.331264 * g + 0.500000 * b + 128.0
                    val cr =  0.500000 * r - 0.418688 * g - 0.081312 * b + 128.0
                    if (cb in 77.0..127.0 && cr in 133.0..173.0 && r > g && g > b) {
                        skinPixels++
                    }
                }
            }

            // Estimate spatial gradient variance across horizontal & vertical steps
            var gradSum = 0.0
            var gradCount = 0L
            val step = Math.max(1, (endX - startX) / 35)
            for (y in startY until (endY - step) step step) {
                for (x in startX until (endX - step) step step) {
                    val p1 = bitmap.getPixel(x, y)
                    val pX = bitmap.getPixel(x + step, y)
                    val pY = bitmap.getPixel(x, y + step)
                    val l1 = 0.2989 * ((p1 shr 16) and 0xFF) + 0.5870 * ((p1 shr 8) and 0xFF) + 0.1140 * (p1 and 0xFF)
                    val lx = 0.2989 * ((pX shr 16) and 0xFF) + 0.5870 * ((pX shr 8) and 0xFF) + 0.1140 * (pX and 0xFF)
                    val ly = 0.2989 * ((pY shr 16) and 0xFF) + 0.5870 * ((pY shr 8) and 0xFF) + 0.1140 * (pY and 0xFF)
                    val gx = lx - l1
                    val gy = ly - l1
                    gradSum += (gx * gx + gy * gy)
                    gradCount++
                }
            }

            bitmap.recycle()

            if (count == 0L) {
                promise.reject("EMPTY_SAMPLE", "No pixels could be sampled")
                return
            }

            val avgR = (totalR / count).toInt()
            val avgG = (totalG / count).toInt()
            val avgB = (totalB / count).toInt()
            val hex = String.format("#%02X%02X%02X", avgR, avgG, avgB)

            val meanLum = totalLuminance / count
            val variance = Math.max(0.0, (totalLuminanceSq / count) - (meanLum * meanLum))
            val stdDev = Math.sqrt(variance)
            val skinRatio = skinPixels.toDouble() / count
            val gradVariance = if (gradCount > 0) gradSum / gradCount else 0.0

            val map = com.facebook.react.bridge.Arguments.createMap().apply {
                putInt("r", avgR)
                putInt("g", avgG)
                putInt("b", avgB)
                putString("hex", hex)
                putDouble("stdDev", stdDev)
                putDouble("skinRatio", skinRatio)
                putDouble("gradVariance", gradVariance)
            }
            promise.resolve(map)
        } catch (e: Exception) {
            promise.reject("EXTRACT_ERROR", e.message, e)
        }
    }
}
