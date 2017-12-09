#include <stdio.h>

const int MAXLINE = 4096;
const int MAXCOLS = 640; // oughta be enough for anybody
const int SENTINEL = -1;

int check_next_line();

int main() {
    int sum = 0;
    int diff;
    while ((diff = check_next_line()) != SENTINEL) {
        sum += diff;
    }
    printf("checksum %d\n", sum);
    return 0;
}

int check_next_line() {
    char line[MAXLINE];
    char *buffer = line;
    int min = SENTINEL;
    int max = SENTINEL;
    int current;
    int offset;
    if (fgets(line, MAXLINE, stdin) != NULL) {
        printf("line: %s", line);
        while (sscanf(buffer, "%d%n", &current, &offset) == 1) {
            buffer += offset;
            if (min == SENTINEL) {
                min = max = current;
            }
            if (min > current) {
                min = current;
            }
            if (max < current) {
                max = current;
            }
        }
        printf("min %d max %d\n", min, max);
        return max - min;
    }
    return SENTINEL;
}