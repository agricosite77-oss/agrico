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

    private fun getLocalFileFromUri(uriString: String): File {
        if (uriString.startsWith("content://")) {
            val uri = Uri.parse(uriString)
            val tempFile = File(reactContext.cacheDir, "temp_input_${System.currentTimeMillis()}.jpg")
            reactContext.contentResolver.openInputStream(uri)?.use { input ->
                tempFile.outputStream().use { output ->
                    input.copyTo(output)
                }
            }
            return tempFile
        }
        val cleanPath = uriString.replace("file://", "")
        return File(cleanPath)
    }

    @ReactMethod
    fun extractSoilColor(filePath: String, promise: Promise) {
        try {
            val file = getLocalFileFromUri(filePath)
            if (!file.exists()) {
                promise.reject("FILE_NOT_FOUND", "File not found at $filePath")
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
                promise.reject("DECODE_ERROR", "Failed to decode image at $filePath")
                return
            }

            // Sample central 80% region of the soil specimen (inside crop box)
            val w = bitmap.width
            val h = bitmap.height
            val startX = Math.max(0, (w * 0.10).toInt())
            val endX = Math.min(w, (w * 0.90).toInt())
            val startY = Math.max(0, (h * 0.10).toInt())
            val endY = Math.min(h, (h * 0.90).toInt())

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

    @ReactMethod
    fun cropImageWithFrame(
        filePath: String,
        frameX: Double,
        frameY: Double,
        frameWidth: Double,
        frameHeight: Double,
        previewWidth: Double,
        previewHeight: Double,
        promise: Promise
    ) {
        try {
            val file = getLocalFileFromUri(filePath)
            if (!file.exists()) {
                promise.reject("FILE_NOT_FOUND", "Image file not found at $filePath")
                return
            }
            val absolutePath = file.absolutePath

            // Read orientation from EXIF
            var rotation = 0
            try {
                val exif = android.media.ExifInterface(absolutePath)
                val orientation = exif.getAttributeInt(
                    android.media.ExifInterface.TAG_ORIENTATION,
                    android.media.ExifInterface.ORIENTATION_NORMAL
                )
                rotation = when (orientation) {
                    android.media.ExifInterface.ORIENTATION_ROTATE_90 -> 90
                    android.media.ExifInterface.ORIENTATION_ROTATE_180 -> 180
                    android.media.ExifInterface.ORIENTATION_ROTATE_270 -> 270
                    else -> 0
                }
            } catch (e: Exception) {
                // Ignore exif errors
            }

            // Downsample if huge (> 2500px) to prevent OOM
            val boundsOpts = android.graphics.BitmapFactory.Options().apply { inJustDecodeBounds = true }
            android.graphics.BitmapFactory.decodeFile(absolutePath, boundsOpts)
            val maxDim = Math.max(boundsOpts.outWidth, boundsOpts.outHeight)
            var sampleSize = 1
            if (maxDim > 2500) {
                sampleSize = 2
            }

            val decodeOpts = android.graphics.BitmapFactory.Options().apply {
                inSampleSize = sampleSize
                inPreferredConfig = android.graphics.Bitmap.Config.ARGB_8888
            }
            var sourceBitmap = android.graphics.BitmapFactory.decodeFile(absolutePath, decodeOpts)
            if (sourceBitmap == null) {
                promise.reject("DECODE_ERROR", "Failed to decode image at $filePath")
                return
            }

            if (rotation != 0) {
                val matrix = android.graphics.Matrix().apply { postRotate(rotation.toFloat()) }
                val rotated = android.graphics.Bitmap.createBitmap(
                    sourceBitmap, 0, 0, sourceBitmap.width, sourceBitmap.height, matrix, true
                )
                if (rotated != sourceBitmap) {
                    sourceBitmap.recycle()
                    sourceBitmap = rotated
                }
            }

            val imgW = sourceBitmap.width.toDouble()
            val imgH = sourceBitmap.height.toDouble()

            val pW = if (previewWidth > 0) previewWidth else imgW
            val pH = if (previewHeight > 0) previewHeight else imgH

            // Image uses resizeMode="cover" (matches <Image ... resizeMode="cover" />)
            val scale = Math.max(pW / imgW, pH / imgH)
            val offX = (imgW * scale - pW) / 2.0
            val offY = (imgH * scale - pH) / 2.0

            val rawCropX = (frameX + offX) / scale
            val rawCropY = (frameY + offY) / scale
            val rawCropW = frameWidth / scale
            val rawCropH = frameHeight / scale

            val cropX = Math.max(0, Math.min(rawCropX.toInt(), sourceBitmap.width - 1))
            val cropY = Math.max(0, Math.min(rawCropY.toInt(), sourceBitmap.height - 1))
            val cropW = Math.max(1, Math.min(rawCropW.toInt(), sourceBitmap.width - cropX))
            val cropH = Math.max(1, Math.min(rawCropH.toInt(), sourceBitmap.height - cropY))

            val cropped = android.graphics.Bitmap.createBitmap(sourceBitmap, cropX, cropY, cropW, cropH)

            val cropFile = File(reactContext.cacheDir, "soil_crop_${System.currentTimeMillis()}.jpg")
            val outStream = java.io.FileOutputStream(cropFile)
            cropped.compress(android.graphics.Bitmap.CompressFormat.JPEG, 92, outStream)
            outStream.flush()
            outStream.close()

            val base64Bytes = cropFile.readBytes()
            val base64String = android.util.Base64.encodeToString(base64Bytes, android.util.Base64.NO_WRAP)

            sourceBitmap.recycle()
            if (cropped != sourceBitmap) {
                cropped.recycle()
            }

            val map = com.facebook.react.bridge.Arguments.createMap().apply {
                putString("uri", "file://${cropFile.absolutePath}")
                putString("base64", base64String)
                putInt("x", cropX)
                putInt("y", cropY)
                putInt("width", cropW)
                putInt("height", cropH)
                putInt("originalWidth", imgW.toInt())
                putInt("originalHeight", imgH.toInt())
            }
            promise.resolve(map)
        } catch (e: Exception) {
            promise.reject("CROP_ERROR", e.message, e)
        }
    }
}
