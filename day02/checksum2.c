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
    int nums[16]; // totally cheating
    int len = 0;
    int current;
    int offset;
    if (fgets(line, MAXLINE, stdin) != NULL) {
        printf("line: %s", line);
        while (sscanf(buffer, "%d%n", &current, &offset) == 1) {
            buffer += offset;
            nums[len++] = current;
        }
        for (int i = 0; i < len; i ++) {
            for (int j = 0; j < len; j++) {
                if (i == j) {
                    continue;
                }

                if (nums[i] > nums[j] && nums[i]%nums[j] == 0) {
                    return nums[i] / nums[j];
                }

                if (nums[j] > nums[i] && nums[j]%nums[i] == 0) {
                    return nums[j] / nums[i];
                }
            }
        }
    }
    return SENTINEL;
}