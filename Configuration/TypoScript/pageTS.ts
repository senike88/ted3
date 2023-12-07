# *******************************************************
# Define content elements in "New Content Element Wizard"
# *******************************************************

mod.wizards.newContentElement.wizardItems.common.elements {
                textmedia > #Reorder to bottom
                bullets > #Reorder to bottom
                table > #Reorder to bottom
                uploads > #Reorder to bottom
                text >
                text {
			iconIdentifier = content-text
			title = Text
			description = LLL:EXT:backend/Resources/Private/Language/locallang_db_new_content_el.xlf:common_regularText_description
			tt_content_defValues {
				CType = text
			}
		}
                image{
			iconIdentifier = content-image
			title = Images
			description = Simple Images (ted3)
			tt_content_defValues {
				CType = image
			}
		}
                ted3gallery{
			iconIdentifier = install-test-image
			title =  Gallery
			description = Simple Thumbnailgallery (ted3)
			tt_content_defValues {
				CType = ted3gallery
			}
		}
               ted3fadegallery{
			iconIdentifier = content-carousel-image
			title = Fadegallery
			description = Simple Imagefader (ted3)
			tt_content_defValues {
				CType = ted3fadegallery
			}
		}
                textmedia {
			iconIdentifier = content-textpic
			title = LLL:EXT:backend/Resources/Private/Language/locallang_db_new_content_el.xlf:common_textMedia_title
			description = LLL:EXT:backend/Resources/Private/Language/locallang_db_new_content_el.xlf:common_textMedia_description
			tt_content_defValues {
				CType = textmedia
				imageorient = 17
			}
		}
                bullets {
			iconIdentifier = content-bullets
			title = LLL:EXT:backend/Resources/Private/Language/locallang_db_new_content_el.xlf:common_bulletList_title
			description = LLL:EXT:backend/Resources/Private/Language/locallang_db_new_content_el.xlf:common_bulletList_description
			tt_content_defValues {
				CType = bullets
			}
		}
		table {
			iconIdentifier = content-table
			title = LLL:EXT:backend/Resources/Private/Language/locallang_db_new_content_el.xlf:common_table_title
			description = LLL:EXT:backend/Resources/Private/Language/locallang_db_new_content_el.xlf:common_table_description
			tt_content_defValues {
				CType = table
			}
		}
                uploads {
			iconIdentifier = content-special-uploads
			title = LLL:EXT:backend/Resources/Private/Language/locallang_db_new_content_el.xlf:special_filelinks_title
			description = LLL:EXT:backend/Resources/Private/Language/locallang_db_new_content_el.xlf:special_filelinks_description
			tt_content_defValues {
				CType = uploads
			}
		}
            

}
mod.wizards.newContentElement.wizardItems.common.show >
mod.wizards.newContentElement.wizardItems.common.show := addToList(header,text,image,textmedia,bullets,ted3gallery,ted3fadegallery,table,uploads)
