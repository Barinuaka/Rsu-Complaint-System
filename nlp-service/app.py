# app.py — RSU NLP Microservice
# Flask API that classifies student complaints using TF-IDF + SVM + VADER

from flask import Flask, request, jsonify
import re
import pickle
import os

import nltk
from nltk.corpus import stopwords
from nltk.stem import WordNetLemmatizer
from nltk.tokenize import word_tokenize

from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.svm import LinearSVC
from sklearn.pipeline import Pipeline
from sklearn.preprocessing import LabelEncoder

from vaderSentiment.vaderSentiment import SentimentIntensityAnalyzer

from training_data import TRAINING_DATA

app = Flask(__name__)

# ─── Category mapping ─────────────────────────────────────────────
CATEGORY_MAP = {
    "Academic":              1,
    "Welfare and Security":  2,
    "Infrastructure":        3,
    "Financial":             4,
    "General Guidance":      5,
}

# ─── VADER urgency thresholds ─────────────────────────────────────
def score_to_urgency(compound: float) -> str:
    if compound <= -0.6:
        return "critical"
    elif compound <= -0.2:
        return "high"
    elif compound <= 0.2:
        return "medium"
    else:
        return "low"

# ─── Text preprocessing ───────────────────────────────────────────
lemmatizer    = WordNetLemmatizer()
stop_words    = set(stopwords.words('english'))

# RSU-specific words to keep (not stopwords in this context)
KEEP_WORDS = {'not', 'no', 'wrong', 'never', 'failed', 'unfair',
              'denied', 'refused', 'missing', 'stolen', 'broken'}

def preprocess(text: str) -> str:
    text   = text.lower()
    text   = re.sub(r'[^a-z\s]', ' ', text)
    tokens = word_tokenize(text)
    tokens = [
        lemmatizer.lemmatize(t) for t in tokens
        if t not in stop_words or t in KEEP_WORDS
    ]
    return ' '.join(tokens)

# ─── Train or load the SVM model ──────────────────────────────────
MODEL_PATH = 'model.pkl'

def train_model():
    print("Training SVM classifier...")
    texts  = [preprocess(item[0]) for item in TRAINING_DATA]
    labels = [item[1] for item in TRAINING_DATA]

    pipeline = Pipeline([
        ('tfidf', TfidfVectorizer(
            ngram_range=(1, 2),   # unigrams + bigrams
            max_features=5000,
            sublinear_tf=True,
        )),
        ('svm', LinearSVC(
            C=1.0,
            max_iter=2000,
        )),
    ])

    pipeline.fit(texts, labels)

    with open(MODEL_PATH, 'wb') as f:
        pickle.dump(pipeline, f)

    print(f"Model trained on {len(texts)} samples. Saved to {MODEL_PATH}")
    return pipeline

def load_model():
    if os.path.exists(MODEL_PATH):
        print("Loading saved model...")
        with open(MODEL_PATH, 'rb') as f:
            return pickle.load(f)
    return train_model()

# Load model on startup
model  = load_model()
vader  = SentimentIntensityAnalyzer()

# ─── API Endpoints ────────────────────────────────────────────────

@app.route('/health', methods=['GET'])
def health():
    return jsonify({'status': 'ok', 'service': 'RSU NLP Microservice'})

@app.route('/classify', methods=['POST'])
def classify():
    data = request.get_json()

    if not data or 'text' not in data:
        return jsonify({'error': 'Missing text field'}), 400

    text = data['text']

    if len(text.strip()) < 5:
        return jsonify({'error': 'Text too short'}), 400

    # Step 1: Preprocess
    processed = preprocess(text)

    # Step 2: SVM classification
    category_name = model.predict([processed])[0]

    # Step 3: Confidence score
    # LinearSVC uses decision function, not probability
    decision_scores = model.decision_function([processed])[0]
    classes         = model.classes_
    max_score       = float(max(decision_scores))
    # Normalise to 0-1 range as a rough confidence
    confidence      = round(min(max(max_score / 3.0, 0.0), 1.0), 4)

    # Step 4: VADER sentiment → urgency
    vader_scores   = vader.polarity_scores(text)
    compound_score = vader_scores['compound']
    urgency_level  = score_to_urgency(compound_score)

    # Step 5: Build response
    response = {
        'category_id':   CATEGORY_MAP.get(category_name, 5),
        'category_name': category_name,
        'urgency_level': urgency_level,
        'confidence':    confidence,
        'vader_score':   round(compound_score, 4),
    }

    print(f"Classified: '{text[:50]}...' → {category_name} | {urgency_level}")
    return jsonify(response)

@app.route('/retrain', methods=['POST'])
def retrain():
    global model
    model = train_model()
    return jsonify({'status': 'retrained', 'samples': len(TRAINING_DATA)})

# ─── Run ──────────────────────────────────────────────────────────
if __name__ == '__main__':
    app.run(host='127.0.0.1', port=5000, debug=True)