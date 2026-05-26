import sys
import json
import joblib
import pandas as pd
import numpy as np
import os
import warnings

# Suppress warnings untuk clean JSON output
warnings.filterwarnings("ignore")

def main():
    try:
        # Get model directory from command line argument atau gunakan default
        if len(sys.argv) > 1:
            model_dir = sys.argv[1]
        else:
            # Fallback: path relatif terhadap script ini
            script_dir = os.path.dirname(os.path.abspath(__file__))
            model_dir = os.path.join(script_dir, '..', 'model')
        
        model_path = os.path.join(model_dir, 'model_rf.pkl')
        
        # Debug: cek model exists
        if not os.path.exists(model_path):
            print(json.dumps({"error": f"Model not found at: {model_path}"}))
            sys.exit(1)
            
        # Load model
        model = joblib.load(model_path)
        
        # Baca input JSON dari stdin
        input_data = sys.stdin.read()
        if not input_data:
            print(json.dumps([]))
            sys.exit(0)
            
        data = json.loads(input_data)
        
        if not data:
            print(json.dumps([]))
            sys.exit(0)
            
        # Ekstrak fitur untuk batch prediction
        # Urutan fitur penting: ['IPK', 'Nilai KHS Mata Kuliah Syarat', 'Nilai Praktikum Mata Kuliah Syarat']
        features_list = []
        for item in data:
            features_list.append([item['ipk'], item['khs'], item['prak']])
            
        X = pd.DataFrame(features_list, columns=['IPK', 'Nilai KHS Mata Kuliah Syarat', 'Nilai Praktikum Mata Kuliah Syarat'])
        
        # Lakukan prediksi (0/1)
        predictions = model.predict(X)
        
        # Lakukan prediksi probabilitas (confidence)
        probabilities = model.predict_proba(X)
        
        # Susun output JSON
        results = []
        for i, item in enumerate(data):
            pred_class = int(predictions[i])
            # Ambil probabilitas untuk kelas yang diprediksi
            confidence = float(probabilities[i][pred_class])
            
            results.append({
                "id": item['id'],
                "prediction": pred_class,
                "confidence": confidence
            })
            
        # Print JSON ke stdout (akan ditangkap oleh PHP proc_open)
        print(json.dumps(results))
        
    except Exception as e:
        print(json.dumps({"error": str(e)}))
        sys.exit(1)

if __name__ == "__main__":
    main()