#include <stdio.h>

const char SENTINEL = -1;

char next_numeral();

int main() {
    char first;
    char current;
    char previous;
    int len = 0;
    int total = 0;

    while((current = next_numeral()) != SENTINEL) {
        if (++len == 1) {
            first = current;
        } else if (current == previous) {
            total += current;
        }
        previous = current;
    }

    if (len > 1 && previous == first) {
        total += first;
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