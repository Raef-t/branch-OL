import 'package:flutter/material.dart';
import '/core/helpers/build_popup_menu_item_in_details_student_view_helper.dart';
import '/core/lists/element_menu_item_in_details_student_view_list.dart';

List<PopupMenuEntry<String>> Function(BuildContext)
buildItemBuilderInPopupMenuInDetailsStudentHelper() {
  return (context) {
    return elementMenuItemInDetailsStudentViewList.map((text) {
      return buildPopupMenuItemInDetailsStudentViewHelper(text: text);
    }).toList();
  };
}
