import sys
import cv2
import pytesseract
import numpy as np
import face_recognition
import json
from difflib import SequenceMatcher

class IDVerifier:
    def __init__(self):
        self.required_text = "republika ng pilipinas"
        self.target_text_variations = [
            "republika ng pilipinas",
            "republika ng pilipinas",
            "republika ng pilipinas",
            "republic of the philippines"
        ]
        
    def enhance_image(self, img):
        gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
        
        clahe = cv2.createCLAHE(clipLimit=2.0, tileGridSize=(8,8))
        contrast = clahe.apply(gray)
        
        denoised = cv2.fastNlMeansDenoising(contrast, None, 10, 7, 21)
        
        kernel = np.array([[0, -1, 0], [-1, 5, -1], [0, -1, 0]])
        sharpened = cv2.filter2D(denoised, -1, kernel)
        
        _, thresh = cv2.threshold(sharpened, 0, 255, cv2.THRESH_BINARY + cv2.THRESH_OTSU)
        
        return thresh
    
    def verify_text(self, image_path):
        try:
            img = cv2.imread(image_path)
            if img is None:
                return False
                
            height, width = img.shape[:2]
            roi = img[0:int(height*0.3), 0:width]
            
            processed = self.enhance_image(roi)
            
            configs = [
                '--oem 3 --psm 6 -l eng+fil',
                '--oem 3 --psm 11 -l eng',
                '--oem 1 --psm 3'
            ]
            
            for config in configs:
                text = pytesseract.image_to_string(processed, config=config).lower()
                for variation in self.target_text_variations:
                    if variation in text:
                        return True
                        
            return False
        except Exception as e:
            print(f"OCR Error: {str(e)}", file=sys.stderr)
            return False
    
    def verify_face(self, id_path, selfie_path):
        try:
            id_img = face_recognition.load_image_file(id_path)
            selfie_img = face_recognition.load_image_file(selfie_path)
            
            id_encodings = face_recognition.face_encodings(id_img)
            selfie_encodings = face_recognition.face_encodings(selfie_img)
            
            if not id_encodings or not selfie_encodings:
                return False
                
            matches = face_recognition.compare_faces(
                [id_encodings[0]], 
                selfie_encodings[0], 
                tolerance=0.55
            )
            
            return matches[0]
        except Exception as e:
            print(f"Face Recognition Error: {str(e)}", file=sys.stderr)
            return False
    
    def verify(self, id_path, selfie_path):
        if not self.verify_text(id_path):
            return False, "Invalid ID: Could not verify 'Republika ng Pilipinas' text"
            
        if not self.verify_face(id_path, selfie_path):
            return False, "Face verification failed: ID and selfie don't match"
            
        return True, "Verification successful!"

if __name__ == "__main__":
    if len(sys.argv) < 3:
        print(json.dumps({"success": False, "message": "Missing image paths"}))
        sys.exit(1)
        
    verifier = IDVerifier()
    success, message = verifier.verify(sys.argv[1], sys.argv[2])
    print(json.dumps({"success": success, "message": message}))




