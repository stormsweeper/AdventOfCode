#include <stdio.h>
#include <stdlib.h>
#include <math.h>


int main(int argc, char *argv[]) {
    if (argc < 2) {
        return 1;
    }
    int input = atoi(argv[1]);
    int dist = 0;

    if (input > 1) {
        // in this pattern, will always end up with a perfect square.
        int width = ceil(sqrt(input));
        if ((width & 1) == 0) {
            width += 1;
        }
    
        int corner = pow(width, 2);
        int endpos = pow(width - 2, 2);
        int mindist = floor(width / 2);
        int maxdist = mindist * 2;
        for ( ; corner > endpos; corner -= maxdist) {
            // if the corner, it'll be max
            if (input == corner) {
                dist = maxdist;
                break;
            }
    
            // if a midpoint, will be min
            if (input == corner - mindist) {
                dist = mindist;
                break;
            }
    
            if (input < corner && input > (corner - maxdist)) {
                dist = mindist + abs((corner - mindist) - input);
                break;
            }
        }
    }

    printf("%d", dist);

    return 0;
}