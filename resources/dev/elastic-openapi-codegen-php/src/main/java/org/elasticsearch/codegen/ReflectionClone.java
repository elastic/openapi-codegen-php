package org.elasticsearch.codegen;

import java.util.Arrays;

public class ReflectionClone {
    public static Object clone(Object source, Object target) {
        Arrays.asList(source.getClass().getFields()).stream().forEach(f -> {
            try {
                f.set(target, f.get(source));
            } catch (IllegalAccessException e) {
                ;
            }
        });
        return target;
    }
}
