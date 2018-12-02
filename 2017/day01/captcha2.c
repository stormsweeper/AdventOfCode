#include <stdio.h>

const char SENTINEL = -1;

char next_numeral();

int main() {
    char current;
    int len = 0;
    int total = 0;
    int nums[2150]; // cheating, I know this is the count of input

    while((current = next_numeral()) != SENTINEL) {
        nums[len] = current;
        len++;
    }

    for (int i = 0; i < len; i++) {
        if (nums[i] == nums[ (i + len/2)%len ]) {
            total += nums[i];
        }
    }
    printf("len: %d sum: %d\n", len, total);
    return 0;
}

char next_numeral() {
    // being lazy and trusting this will be ASCII/UTF-8 numerals only
    int next = getchar();
    if (next != EOF && next >= '0' && next <= '9') {
        return next - '0';
    }
    return SENTINEL;
}