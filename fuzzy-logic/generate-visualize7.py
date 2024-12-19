import numpy as np
import skfuzzy as fuzz
import matplotlib.pyplot as plt
import json
import os

maintenance_frequency = np.arange(0, 13, 1)

low = fuzz.trapmf(maintenance_frequency, [0, 0, 2, 4])
moderate = fuzz.trimf(maintenance_frequency, [3, 6, 9])
high = fuzz.trapmf(maintenance_frequency, [8, 10, 12, 12])

def get_maintenance_category(value):
    low_value = fuzz.interp_membership(maintenance_frequency, low, value)
    moderate_value = fuzz.interp_membership(maintenance_frequency, moderate, value)
    high_value = fuzz.interp_membership(maintenance_frequency, high, value)

    memberships = {
        "low": low_value,
        "moderate": moderate_value,
        "high": high_value
    }

    return max(memberships, key=memberships.get)

data = [{"value": value, "category": get_maintenance_category(value)} for value in range(13)]

output_dir = "categories-data"
os.makedirs(output_dir, exist_ok=True)

output_path = os.path.join(output_dir, "maintenance_frequency.json")
with open(output_path, "w", encoding="utf-8") as f:
    json.dump(data, f, ensure_ascii=False, indent=4)

plt.figure(figsize=(10, 6))
plt.plot(maintenance_frequency, low, label="Low (Низька частота обслуговувань)", color="blue")
plt.plot(maintenance_frequency, moderate, label="Moderate (Помірна частота обслуговувань)", color="green")
plt.plot(maintenance_frequency, high, label="High (Висока частота обслуговувань)", color="orange")

plt.title("Нечіткі множини для частоти обслуговувань обладнання", fontsize=14)
plt.xlabel("Частота обслуговувань (разів на рік)", fontsize=12)
plt.ylabel("Ступінь належності", fontsize=12)
plt.legend(loc="best")
plt.grid(True)

plt.show()
