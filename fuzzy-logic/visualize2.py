import numpy as np
import skfuzzy as fuzz
import matplotlib.pyplot as plt

# Створюємо універсум для ймовірності
probability = np.arange(0, 101, 1)

# Визначаємо нечіткі множини
low = fuzz.trapmf(probability, [0, 0, 20, 40])        # Низька
moderate = fuzz.trimf(probability, [20, 50, 80])     # Помірна
high = fuzz.trapmf(probability, [60, 80, 100, 100])  # Висока
critical = fuzz.trapmf(probability, [90, 100, 100, 100])  # Критична

# Візуалізація нечітких множин
plt.figure(figsize=(10, 6))

# Кожна нечітка множина на окремій кривій
plt.plot(probability, low, label="Low (Низька)", color="blue")
plt.plot(probability, moderate, label="Moderate (Помірна)", color="green")
plt.plot(probability, high, label="High (Висока)", color="orange")
plt.plot(probability, critical, label="Critical (Критична)", color="red")

# Додавання підписів і стилів
plt.title("Нечіткі множини для ймовірності виникнення НС", fontsize=14)
plt.xlabel("Ймовірність (%)", fontsize=12)
plt.ylabel("Ступінь належності", fontsize=12)
plt.legend(loc="best")
plt.grid(True)

# Відображення графіка
plt.show()
