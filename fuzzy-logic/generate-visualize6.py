import numpy as np
import skfuzzy as fuzz
import matplotlib.pyplot as plt
import json
import os

training_count = np.arange(0, 11, 1)

low = fuzz.trapmf(training_count, [0, 0, 2, 4])
moderate = fuzz.trimf(training_count, [3, 5, 7])
high = fuzz.trapmf(training_count, [6, 8, 10, 10])

def get_training_category(value):
    low_value = fuzz.interp_membership(training_count, low, value)
    moderate_value = fuzz.interp_membership(training_count, moderate, value)
    high_value = fuzz.interp_membership(training_count, high, value)

    memberships = {
        "low": low_value,
        "moderate": moderate_value,
        "high": high_value
    }

    return max(memberships, key=memberships.get)

data = [{"value": value, "category": get_training_category(value)} for value in range(11)]

output_dir = "categories-data"
os.makedirs(output_dir, exist_ok=True)

output_path = os.path.join(output_dir, "training_count.json")
with open(output_path, "w", encoding="utf-8") as f:
    json.dump(data, f, ensure_ascii=False, indent=4)

plt.figure(figsize=(10, 6))
plt.plot(training_count, low, label="Low (Низька кількість навчань)", color="blue")
plt.plot(training_count, moderate, label="Moderate (Помірна кількість навчань)", color="green")
plt.plot(training_count, high, label="High (Висока кількість навчань)", color="orange")

plt.title("Нечіткі множини для кількості навчань", fontsize=14)
plt.xlabel("Кількість навчань", fontsize=12)
plt.ylabel("Ступінь належності", fontsize=12)
plt.legend(loc="best")
plt.grid(True)

plt.show()
