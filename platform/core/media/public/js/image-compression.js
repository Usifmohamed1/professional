(function () {
    if (typeof Dropzone === 'undefined' || !Dropzone.optionsForElement) {
        return
    }

    const originalOptionsForElement = Dropzone.optionsForElement
    const targetSize = 1024 * 1024
    const supportedTypes = ['image/jpeg', 'image/png', 'image/webp']

    async function compressImage(file) {
        if (file.size <= targetSize || !supportedTypes.includes(file.type) || !window.createImageBitmap) {
            return file
        }

        const bitmap = await window.createImageBitmap(file)
        const canvas = document.createElement('canvas')
        const context = canvas.getContext('2d')

        if (!context) {
            bitmap.close()
            return file
        }

        let width = bitmap.width
        let height = bitmap.height
        let quality = 0.85
        let lastBlob = null

        try {
            while (width > 0 && height > 0) {
                canvas.width = width
                canvas.height = height
                context.drawImage(bitmap, 0, 0, width, height)

                const blob = await new Promise((resolve) => {
                    canvas.toBlob(resolve, file.type, quality)
                })

                if (!blob || blob.type !== file.type) {
                    return file
                }

                lastBlob = blob

                if (blob.size <= targetSize) {
                    return new File([blob], file.name, { type: file.type, lastModified: file.lastModified })
                }

                if ((file.type === 'image/jpeg' || file.type === 'image/webp') && quality > 0.35) {
                    quality = Math.max(0.35, quality - 0.1)
                    continue
                }

                width = Math.max(1, Math.floor(width * 0.8))
                height = Math.max(1, Math.floor(height * 0.8))
                quality = 0.85
            }

            return lastBlob && lastBlob.size <= targetSize
                ? new File([lastBlob], file.name, { type: file.type, lastModified: file.lastModified })
                : file
        } finally {
            bitmap.close()
        }
    }

    Dropzone.optionsForElement = function (element) {
        const options = originalOptionsForElement.call(this, element) || {}

        return {
            ...options,
            transformFile(file, done) {
                compressImage(file).then(done).catch(() => done(file))
            },
        }
    }
})()