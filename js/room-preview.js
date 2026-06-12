/**
 * Room Preview Feature - Dynamic Background Removal Version
 * Optimized for full-canvas background and interactive product placement
 */

class RoomPreview {
    constructor(productId, productImg) {
        this.productId = productId;
        this.productImg = productImg;
        this.canvas = null;
        this.productObject = null; // Store reference to product for dynamic filter updates
        this.modal = document.getElementById('roomPreviewModal');
        this.uploadInput = document.getElementById('roomImageUpload');
        this.sensitivityInput = document.getElementById('bgSensitivity');
        this.previewArea = document.getElementById('previewArea');
        this.uploadSection = document.getElementById('uploadSection');
        this.controlsSection = document.getElementById('controlsSection');

        this.init();
    }

    init() {
        // Initialize Fabric Canvas with container dimensions (initially)
        const container = document.querySelector('.preview-container');
        this.canvas = new fabric.Canvas('roomCanvas', {
            width: container.clientWidth,
            height: container.clientHeight,
            backgroundColor: '#ffffff',
            preserveObjectStacking: true
        });

        // Event Listeners
        this.uploadInput.addEventListener('change', (e) => this.handleUpload(e));

        // Background sensitivity slider
        if (this.sensitivityInput) {
            this.sensitivityInput.addEventListener('input', () => this.updateFilters());
        }

        document.getElementById('downloadPreview').addEventListener('click', () => this.download());
        document.getElementById('uploadNewImage').addEventListener('click', () => this.reset());
        document.querySelectorAll('.room-preview-close, #closePreview').forEach(el => {
            el.addEventListener('click', () => this.close());
        });

        window.addEventListener('resize', () => this.resizeCanvas());
    }

    open() {
        this.reset();
        this.modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
        setTimeout(() => this.resizeCanvas(), 300);
    }

    close() {
        this.modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    reset() {
        this.canvas.clear();
        this.productObject = null;
        this.canvas.setBackgroundColor('#ffffff', this.canvas.renderAll.bind(this.canvas));
        this.uploadSection.style.display = 'block';
        this.previewArea.style.display = 'none';
        this.controlsSection.style.display = 'none';
        this.uploadInput.value = '';
    }

    handleUpload(e) {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (f) => {
            const data = f.target.result;
            fabric.Image.fromURL(data, (roomImg) => {
                if (!roomImg) return;

                const canvasAspect = this.canvas.width / this.canvas.height;
                const imgAspect = roomImg.width / roomImg.height;

                let scale;
                if (imgAspect > canvasAspect) {
                    scale = this.canvas.height / roomImg.height;
                } else {
                    scale = this.canvas.width / roomImg.width;
                }

                this.canvas.setBackgroundImage(roomImg, () => {
                    this.canvas.renderAll();
                    this.uploadSection.style.display = 'none';
                    this.previewArea.style.display = 'block';
                    this.controlsSection.style.display = 'flex';
                    this.loadProduct();
                }, {
                    scaleX: scale,
                    scaleY: scale,
                    originX: 'center',
                    originY: 'center',
                    left: this.canvas.width / 2,
                    top: this.canvas.height / 2
                });
            });
        };
        reader.readAsDataURL(file);
    }

    loadProduct() {
        if (!this.productImg) return;

        let finalPath = this.productImg.replace(/img\/img\//g, 'img/');
        if (!finalPath.startsWith('http') && !finalPath.startsWith('/')) {
            finalPath = window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, '/') + finalPath;
        }

        fabric.Image.fromURL(finalPath, (prodImg) => {
            if (!prodImg) return;
            this.productObject = prodImg;

            this.updateFilters();

            const shadow = new fabric.Shadow({
                color: 'rgba(0,0,0,0.3)',
                blur: 25,
                offsetX: 0,
                offsetY: 15
            });

            const targetHeight = this.canvas.height * 0.55;
            const scale = targetHeight / prodImg.height;

            prodImg.set({
                scaleX: scale,
                scaleY: scale,
                left: this.canvas.width / 2,
                top: this.canvas.height / 2,
                originX: 'center',
                originY: 'center',
                shadow: shadow,
                cornerColor: '#bc987e',
                cornerStyle: 'circle',
                cornerSize: 14,
                transparentCorners: false,
                borderColor: '#bc987e',
                borderScaleFactor: 2,
                hasRotatingPoint: true,
                padding: 15
            });

            this.canvas.add(prodImg);
            this.canvas.setActiveObject(prodImg);
            this.canvas.bringToFront(prodImg);
            this.canvas.renderAll();
        }, { crossOrigin: 'anonymous' });
    }

    updateFilters() {
        if (!this.productObject) return;

        const sensitivity = this.sensitivityInput ? parseInt(this.sensitivityInput.value) / 100 : 0.25;

        // Reset filters
        this.productObject.filters = [];

        // 1. Remove Background Color
        // We target white by default but with dynamic sensitivity
        this.productObject.filters.push(new fabric.Image.filters.RemoveColor({
            color: '#FFFFFF',
            distance: sensitivity
        }));

        // 2. Add Contrast to soften edges
        this.productObject.filters.push(new fabric.Image.filters.Contrast({ contrast: 0.1 }));

        this.productObject.applyFilters();
        this.canvas.renderAll();
    }

    download() {
        const dataURL = this.canvas.toDataURL({
            format: 'png',
            quality: 1,
            multiplier: 2
        });
        const link = document.createElement('a');
        link.download = `my-room-design-${this.productId}.png`;
        link.href = dataURL;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    resizeCanvas() {
        const container = document.querySelector('.preview-container');
        if (!container) return;

        const width = container.clientWidth;
        const height = container.clientHeight;
        if (width === 0 || height === 0) return;

        this.canvas.setDimensions({ width, height });

        const backImg = this.canvas.backgroundImage;
        if (backImg) {
            const canvasAspect = width / height;
            let newScale;
            if (backImg.width / backImg.height > canvasAspect) {
                newScale = height / backImg.height;
            } else {
                newScale = width / backImg.width;
            }

            backImg.set({
                scaleX: newScale,
                scaleY: newScale,
                left: width / 2,
                top: height / 2
            });
        }

        this.canvas.renderAll();
    }
}

window.initRoomPreview = function (productId, productImg) {
    window.roomPreview = new RoomPreview(productId, productImg);
};
