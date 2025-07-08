let stream;
        let cameraActive = false;
        let idDescriptor = null;
        let selfieDescriptor = null;
        let faceDetectionInterval = null;
        let captureSequenceTimeout = null;
        
        // Load face-api.js models
        async function loadFaceModels() {
            try {
                await faceapi.nets.tinyFaceDetector.loadFromUri('../helpers/face_id_model');
                await faceapi.nets.faceLandmark68Net.loadFromUri('./../helpers/face_id_model');
                await faceapi.nets.faceRecognitionNet.loadFromUri('../helpers/face_id_model');
                console.log('Face recognition models loaded successfully');
            } catch (err) {
                console.error('Error loading face models:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Face Recognition Error',
                    text: 'Failed to load face recognition models. Please refresh the page.',
                    confirmButtonColor: '#3B82F6',
                    customClass: {
                        popup: 'rounded-xl',
                        confirmButton: 'px-4 py-2 rounded-lg'
                    }
                });
            }
        }
        
        // Call this when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadFaceModels();
            
            // Initialize event listeners
            document.getElementById('retakeBtn').addEventListener('click', retakeSelfie);
            document.getElementById('captureBtn').addEventListener('click', startCaptureSequence);
            document.getElementById('verificationForm').addEventListener('submit', handleVerificationSubmit);
            document.getElementById('personalInfoForm').addEventListener('submit', handlePersonalInfoSubmit);
            document.getElementById('id_photo').addEventListener('change', handleIdUpload);
            
            // Initialize drag and drop
            initDragAndDrop();
        });
        
        // Open/close popup functions
        function openVerificationPopup() {
            document.getElementById('verificationPopup').classList.add('active');
            startCamera();
            document.body.style.overflow = 'hidden';
        }
        
        function closeVerificationPopup() {
            document.getElementById('verificationPopup').classList.remove('active');
            stopCamera();
            document.body.style.overflow = 'auto';
        }
        
        function openPersonalInfoPopup(result) {
            const idPath = result?.id_path || localStorage.getItem('id_path') || '';
            const selfiePath = result?.selfie_path || localStorage.getItem('selfie_path') || '';
            
            document.getElementById('id_path').value = idPath;
            document.getElementById('selfie_path').value = selfiePath;
            
            document.getElementById('personalInfoPopup').classList.add('active');
        }

        function closePersonalInfoPopup() {
            document.getElementById('personalInfoPopup').classList.remove('active');
        }

        function closeAllPopups() {
            closeVerificationPopup();
            closePersonalInfoPopup();
        }
        
        // Camera functions with face detection
        async function startCamera() {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        facingMode: 'user',
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    },
                    audio: false 
                });
                const video = document.getElementById('video');
                video.srcObject = stream;
                cameraActive = true;
                
                // Start face detection on video stream
                detectFaces();
            } catch (err) {
                console.error("Camera error:", err);
                Swal.fire({
                    icon: 'error',
                    title: 'Camera Error',
                    text: 'Please enable camera access to continue',
                    confirmButtonColor: '#3B82F6',
                    customClass: {
                        popup: 'rounded-xl',
                        confirmButton: 'px-4 py-2 rounded-lg'
                    }
                });
            }
        }
        
        // Face detection function
        async function detectFaces() {
            if (!cameraActive) return;
            
            const video = document.getElementById('video');
            const canvas = document.getElementById('faceDetectionCanvas');
            const displaySize = { width: video.videoWidth, height: video.videoHeight };
            
            faceapi.matchDimensions(canvas, displaySize);
            
            try {
                const detections = await faceapi.detectAllFaces(video, 
                    new faceapi.TinyFaceDetectorOptions({ scoreThreshold: 0.6 }))
                    .withFaceLandmarks()
                    .withFaceDescriptors();
                
                // Draw detections (optional - can remove if you don't want visual feedback)
                const resizedDetections = faceapi.resizeResults(detections, displaySize);
                canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
                faceapi.draw.drawDetections(canvas, resizedDetections);
                faceapi.draw.drawFaceLandmarks(canvas, resizedDetections);
            } catch (err) {
                console.error("Face detection error:", err);
            }
            
            // Continue detecting
            faceDetectionInterval = requestAnimationFrame(detectFaces);
        }
        
        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                cameraActive = false;
                stream = null;
            }
            if (faceDetectionInterval) {
                cancelAnimationFrame(faceDetectionInterval);
                faceDetectionInterval = null;
            }
            if (captureSequenceTimeout) {
                clearTimeout(captureSequenceTimeout);
                captureSequenceTimeout = null;
            }
        }
        
        // Enhanced face verification
        async function verifyFaces(idImage, selfieImage) {
            try {
                // Load both images
                const idImg = await faceapi.bufferToImage(idImage);
                const selfieImg = await faceapi.bufferToImage(selfieImage);
                
                // Detect all faces in both images with landmarks and descriptors
                const idDetections = await faceapi.detectAllFaces(idImg, 
                    new faceapi.TinyFaceDetectorOptions({ scoreThreshold: 0.6 }))
                    .withFaceLandmarks()
                    .withFaceDescriptors();
                    
                const selfieDetections = await faceapi.detectAllFaces(selfieImg, 
                    new faceapi.TinyFaceDetectorOptions({ scoreThreshold: 0.6 }))
                    .withFaceLandmarks()
                    .withFaceDescriptors();
                
                // Verification checks
                if (idDetections.length === 0) {
                    throw new Error('No face detected in ID photo');
                }
                
                if (selfieDetections.length === 0) {
                    throw new Error('No face detected in selfie');
                }
                
                if (idDetections.length > 1) {
                    throw new Error('Multiple faces detected in ID photo');
                }
                
                if (selfieDetections.length > 1) {
                    throw new Error('Multiple faces detected in selfie');
                }
                
                // Get the descriptors
                const idDescriptor = idDetections[0].descriptor;
                const selfieDescriptor = selfieDetections[0].descriptor;
                
                // Calculate distance and similarity
                const distance = faceapi.euclideanDistance(idDescriptor, selfieDescriptor);
                const similarityScore = ((1 - distance) * 100).toFixed(1);
                
                // Strict threshold - only accept very close matches
                if (distance > 0.45) {
                    throw new Error(`Face match too low (${similarityScore}% similarity)`);
                }
                
                return {
                    success: true,
                    similarity: similarityScore,
                    distance: distance
                };
                
            } catch (error) {
                return {
                    success: false,
                    error: error.message
                };
            }
        }
        
        // Start automatic capture sequence
        async function startCaptureSequence() {
            if (!cameraActive) return false;
            
            // Require ID upload first
            const idFile = document.getElementById('id_photo').files[0];
            if (!idFile) {
                Swal.fire({
                    icon: 'error',
                    title: 'ID Required',
                    text: 'Please upload your ID document first',
                    confirmButtonColor: '#3B82F6'
                });
                return false;
            }
            
            // Disable capture button during sequence
            const captureBtn = document.getElementById('captureBtn');
            captureBtn.disabled = true;
            captureBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Preparing...';
            
            // Show initial instructions
            await Swal.fire({
                title: 'Face Verification',
                html: `We'll automatically capture 3 photos:<br><br>
                       1. Looking straight<br>
                       2. Turn head slightly left<br>
                       3. Turn head slightly right<br><br>
                       Please keep your face within the circle`,
                icon: 'info',
                confirmButtonText: 'Start',
                confirmButtonColor: '#3B82F6',
                allowOutsideClick: false
            });
            
            // Start the capture sequence
            const faceOutline = document.querySelector('.face-outline');
            const captureIndicator = document.getElementById('captureIndicator');
            const captureStepText = document.getElementById('captureStepText');
            
            // Array of capture steps
            const captureSteps = [
                { text: "Looking straight", delay: 3000 },
                { text: "Turn slightly left", delay: 3000 },
                { text: "Turn slightly right", delay: 3000 }
            ];
            
            const captures = [];
            
            try {
                for (let i = 0; i < captureSteps.length; i++) {
                    const step = captureSteps[i];
                    
                    // Update UI for current step
                    faceOutline.classList.add('capturing');
                    captureStepText.textContent = step.text;
                    captureIndicator.classList.remove('hidden');
                    
                    // Wait for the specified delay
                    await new Promise(resolve => {
                        captureSequenceTimeout = setTimeout(resolve, step.delay);
                    });
                    
                    // Capture frame
                    const captureResult = await captureFrame();
                    if (!captureResult.success) {
                        throw new Error('Failed to capture image');
                    }
                    captures.push(captureResult.blob);
                    
                    // Brief pause between captures
                    if (i < captureSteps.length - 1) {
                        faceOutline.classList.remove('capturing');
                        captureIndicator.classList.add('hidden');
                        await new Promise(resolve => setTimeout(resolve, 1000));
                    }
                }
                
                // Verify all captures match
                const referenceCapture = captures[0];
                let allMatch = true;
                
                for (let i = 1; i < captures.length; i++) {
                    const result = await verifyFaces(referenceCapture, captures[i]);
                    if (!result.success || result.distance > 0.5) {
                        allMatch = false;
                        break;
                    }
                }
                
                if (!allMatch) {
                    throw new Error('Your movements didn\'t match expected patterns');
                }
                
                // Verify against ID photo
                const verificationResult = await verifyFaces(idFile, referenceCapture);
                
                if (!verificationResult.success) {
                    throw new Error(verificationResult.error);
                }
                
                // Show success and update UI
                displayVerificationSuccess(referenceCapture, verificationResult.similarity);
                
            } catch (error) {
                // Clean up UI
                faceOutline.classList.remove('capturing');
                captureIndicator.classList.add('hidden');
                captureBtn.disabled = false;
                captureBtn.innerHTML = '<i class="fas fa-camera mr-1"></i> Start Verification';
                
                Swal.fire({
                    icon: 'error',
                    title: 'Verification Failed',
                    text: error.message,
                    confirmButtonColor: '#3B82F6'
                });
                return false;
            } finally {
                // Clean up UI
                faceOutline.classList.remove('capturing');
                captureIndicator.classList.add('hidden');
                captureBtn.disabled = false;
                captureBtn.innerHTML = '<i class="fas fa-camera mr-1"></i> Start Verification';
            }
        }
        
        async function captureFrame() {
            try {
                const video = document.getElementById('video');
                const canvas = document.getElementById('canvas');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const ctx = canvas.getContext('2d');
                
                // Draw mirrored image to canvas
                ctx.save();
                ctx.scale(-1, 1);
                ctx.drawImage(video, 0, 0, canvas.width * -1, canvas.height);
                ctx.restore();
                
                // Convert to blob
                return new Promise((resolve) => {
                    canvas.toBlob((blob) => {
                        resolve({
                            success: true,
                            blob: blob
                        });
                    }, 'image/jpeg', 0.9);
                });
            } catch (error) {
                console.error("Frame capture error:", error);
                return { success: false };
            }
        }
        
        function displayVerificationSuccess(captureBlob, similarityScore) {
            const preview = document.getElementById('selfieImage');
            preview.src = URL.createObjectURL(captureBlob);
            
            document.getElementById('selfiePreview').classList.remove('hidden');
            document.getElementById('captureBtn').classList.add('hidden');
            document.getElementById('retakeBtn').classList.remove('hidden');
            document.getElementById('cameraBox').classList.add('hidden');
            
            // Convert blob to base64 for form submission
            blobToBase64(captureBlob).then(base64 => {
                document.getElementById('selfie_data').value = base64;
            });
            
            // Show match result
            const faceMatchResult = document.getElementById('faceMatchResult');
            const faceMatchText = document.getElementById('faceMatchText');
            const similarityScoreText = document.getElementById('similarityScore');
            const faceMatchIcon = document.getElementById('faceMatchIcon');
            
            faceMatchResult.classList.remove('hidden');
            faceMatchResult.className = 'face-match-success';
            faceMatchText.textContent = 'Verification Successful';
            faceMatchIcon.className = 'fas fa-check-circle';
            similarityScoreText.textContent = `Similarity: ${similarityScore}%`;
            
            Swal.fire({
                icon: 'success',
                title: 'Verification Successful',
                html: `Face match confirmed with ${similarityScore}% similarity`,
                confirmButtonColor: '#3B82F6'
            });
        }
        
        function retakeSelfie() {
            document.getElementById('selfiePreview').classList.add('hidden');
            document.getElementById('captureBtn').classList.remove('hidden');
            document.getElementById('retakeBtn').classList.add('hidden');
            document.getElementById('cameraBox').classList.remove('hidden');
            document.getElementById('faceMatchResult').classList.add('hidden');
            return false;
        }
        
        // Helper function to convert blob to base64
        function blobToBase64(blob) {
            return new Promise((resolve) => {
                const reader = new FileReader();
                reader.onloadend = () => resolve(reader.result);
                reader.readAsDataURL(blob);
            });
        }
        
        // Clear preview
        function clearPreview(elementId) {
            const previewElement = document.getElementById(elementId);
            previewElement.src = '';
            
            if (elementId === 'idPreview') {
                document.getElementById('idPreviewContainer').classList.add('hidden');
                document.getElementById('id_photo').value = '';
                idDescriptor = null;
            } else if (elementId === 'selfieImage') {
                document.getElementById('selfiePreview').classList.add('hidden');
                document.getElementById('selfie_data').value = '';
                document.getElementById('retakeBtn').classList.add('hidden');
                document.getElementById('captureBtn').classList.remove('hidden');
                document.getElementById('cameraBox').classList.remove('hidden');
                document.getElementById('faceMatchResult').classList.add('hidden');
                selfieDescriptor = null;
            }
        }
        
        // Handle ID upload
        async function handleIdUpload(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            // Check file size
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: 'Please upload a file smaller than 5MB',
                    confirmButtonColor: '#3B82F6',
                    customClass: {
                        popup: 'rounded-xl',
                        confirmButton: 'px-4 py-2 rounded-lg'
                    }
                });
                this.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = async function(event) {
                const preview = document.getElementById('idPreview');
                preview.src = event.target.result;
                document.getElementById('idPreviewContainer').classList.remove('hidden');
                document.getElementById('idFileName').textContent = file.name;
                
                try {
                    // Detect face in ID photo
                    const img = await faceapi.bufferToImage(file);
                    const detections = await faceapi.detectAllFaces(img, 
                        new faceapi.TinyFaceDetectorOptions({ scoreThreshold: 0.6 }))
                        .withFaceLandmarks()
                        .withFaceDescriptors();
                    
                    if (detections.length === 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'No Face Detected',
                            text: 'We couldn\'t detect a face in your ID photo. Please upload a clear image where your face is visible.',
                            confirmButtonColor: '#3B82F6',
                            customClass: {
                                popup: 'rounded-xl',
                                confirmButton: 'px-4 py-2 rounded-lg'
                            }
                        });
                        clearPreview('idPreview');
                        return;
                    }
                    
                    if (detections.length > 1) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Multiple Faces',
                            text: 'We detected multiple faces in your ID photo. Please upload an image with only your face visible.',
                            confirmButtonColor: '#3B82F6',
                            customClass: {
                                popup: 'rounded-xl',
                                confirmButton: 'px-4 py-2 rounded-lg'
                            }
                        });
                        clearPreview('idPreview');
                        return;
                    }
                    
                    // Store the face descriptor for later comparison
                    idDescriptor = detections[0].descriptor;
                } catch (err) {
                    console.error("ID face detection error:", err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Processing Error',
                        text: 'Failed to process your ID photo. Please try again.',
                        confirmButtonColor: '#3B82F6',
                        customClass: {
                            popup: 'rounded-xl',
                            confirmButton: 'px-4 py-2 rounded-lg'
                        }
                    });
                }
            };
            reader.readAsDataURL(file);
        }

        // Initialize drag and drop for ID upload
        function initDragAndDrop() {
            const uploadContainer = document.getElementById('uploadContainer');
            
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadContainer.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                uploadContainer.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadContainer.addEventListener(eventName, unhighlight, false);
            });

            function highlight() {
                uploadContainer.classList.add('dragover');
            }

            function unhighlight() {
                uploadContainer.classList.remove('dragover');
            }

            uploadContainer.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                
                if (files.length > 0) {
                    document.getElementById('id_photo').files = files;
                    const event = new Event('change');
                    document.getElementById('id_photo').dispatchEvent(event);
                }
            }
        }

        // Handle verification form submission
        async function handleVerificationSubmit(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
            
            try {
                // Verify both files are present
                const idFile = document.getElementById('id_photo').files[0];
                const selfieData = document.getElementById('selfie_data').value;
                
                if (!idFile || !selfieData) {
                    throw new Error('Please complete both ID upload and selfie capture');
                }
                
                // Convert selfie data back to blob for verification
                const selfieBlob = await (await fetch(selfieData)).blob();
                
                // Final verification
                const verificationResult = await verifyFaces(idFile, selfieBlob);
                
                if (!verificationResult.success) {
                    throw new Error(verificationResult.error);
                }
                
                // Prepare form data
                const formData = new FormData(this);
                formData.append('similarity_score', verificationResult.similarity);
                
                // Submit to server
                const response = await fetch( BASE_URL + 'controllers/routes.php?action=checkId', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Store paths for personal info form
                    if (result.id_path) {
                        localStorage.setItem('id_path', result.id_path);
                    }
                    if (result.selfie_path) {
                        localStorage.setItem('selfie_path', result.selfie_path);
                    }
                    
                    // Open personal info form
                    closeVerificationPopup();
                    openPersonalInfoPopup(result);
                } else {
                    throw new Error(result.message || 'Verification failed on server');
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Verification Error',
                    text: error.message,
                    confirmButtonColor: '#3B82F6'
                });
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> Complete Verification';
            }
        }

        async function handlePersonalInfoSubmit(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitPersonalInfo');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

            const idNumber = document.getElementById('idNumber').value.trim();
            const firstName = document.getElementById('firstName').value.trim();
            const lastName = document.getElementById('lastName').value.trim();
            const middleName = document.getElementById('middleName').value.trim();
            const sex = document.getElementById('sex').value;

            // ID Number: must be numbers only
            if (!/^\d+$/.test(idNumber)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid ID Number',
                    text: 'ID Number must contain numbers only.',
                    confirmButtonColor: '#3B82F6'
                });
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save"></i> Submit Information';
                return;
            }

            // Names: must be letters only
            const nameRegex = /^[A-Za-z\s\-'.]+$/;
            if (!nameRegex.test(firstName) || !nameRegex.test(lastName) || (middleName && !nameRegex.test(middleName))) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Name Format',
                    text: 'Names can only contain letters, spaces, apostrophes, and hyphens.',
                    confirmButtonColor: '#3B82F6'
                });
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save"></i> Submit Information';
                return;
            }
            
            try {
                const formData = new FormData(this);
                
                // Ensure paths are included (from hidden fields)
                const idPath = document.getElementById('id_path').value;
                const selfiePath = document.getElementById('selfie_path').value;
                
                if (idPath) formData.set('id_path', idPath);
                if (selfiePath) formData.set('selfie_path', selfiePath);
                
                // Add user ID
                const userId = getCurrentUserId();
                if (userId) {
                    formData.append('user_id', userId);
                } else {
                    throw new Error('User session expired');
                }
                
                const response = await fetch( BASE_URL + 'controllers/routes.php?action=saveId', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Please wait 2-3 working days to approve.',
                        confirmButtonColor: '#3B82F6'
                    }).then(() => {
                        // Clean up
                        localStorage.removeItem('id_path');
                        localStorage.removeItem('selfie_path');
                        closePersonalInfoPopup();
                    });
                } else {
                    throw new Error(result.message || 'Failed to save information');
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message,
                    confirmButtonColor: '#3B82F6'
                });
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save"></i> Submit Information';
            }
        }

        // Close when clicking outside
        document.addEventListener('click', function(event) {
            if (event.target === document.getElementById('verificationPopup')) {
                closeVerificationPopup();
            }
            if (event.target === document.getElementById('personalInfoPopup')) {
                closePersonalInfoPopup();
            }
        });