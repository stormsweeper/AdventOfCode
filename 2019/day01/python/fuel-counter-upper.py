import sys, math

def calcFuel(mass):
    fuel = math.floor(mass/3) - 2
    return max(0, fuel)

modules = open(sys.argv[1])

total_module_fuel = 0
total_fuel_fuel = 0

for module in modules:
    module = module.strip()
    if len(module) < 1:
        continue
    module_fuel = calcFuel(int(module))
    total_module_fuel += module_fuel
    if module_fuel >= 9:
        fuel_mass = module_fuel
        while fuel_mass >= 9:
            fuel_fuel = calcFuel(fuel_mass)
            total_fuel_fuel += fuel_fuel
            fuel_mass = fuel_fuel
    

print(total_module_fuel)
print(total_fuel_fuel)
print(total_module_fuel + total_fuel_fuel)
