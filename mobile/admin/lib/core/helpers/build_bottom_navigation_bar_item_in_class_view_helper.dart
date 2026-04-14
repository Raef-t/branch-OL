import 'package:flutter/material.dart';
import '/features/class/presentation/view/widgets/custom_image_and_text_in_bottom_navigation_bar_in_class_view.dart';

BottomNavigationBarItem buildBottomNavigationBarItemInClassViewHelper({
  required Image image,
  required String text,
}) {
  return BottomNavigationBarItem(
    icon: CustomImageAndTextInBottomNavigationBarInClassView(
      image: image,
      text: text,
    ),
    label: '',
  );
}
